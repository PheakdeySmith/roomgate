<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    /**
     * Download a document
     * This method allows both tenants and landlords to download documents
     * 
     * @param Document $document The document to download
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download(Document $document)
    {
        $user = Auth::user();
        
        // Check if user has permission to download this document
        $canDownload = false;
        
        // Tenant can download their own documents
        if ($user->id === $document->user_id) {
            $canDownload = true;
        }
        
        // Landlord can download documents from their tenants
        if ($user->hasRole('landlord') && $document->user->landlord_id === $user->id) {
            $canDownload = true;
        }
        
        if (!$canDownload) {
            abort(403, 'Unauthorized access');
        }
        
        $filePath = public_path($document->file_path);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Document not found.');
        }
        
        // Get the file extension to determine content type
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $contentType = 'application/octet-stream'; // Default
        
        if ($extension === 'pdf') {
            $contentType = 'application/pdf';
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $contentType = 'image/' . $extension;
        }
        
        return response()->download($filePath, $document->name . '.' . $extension, ['Content-Type' => $contentType]);
    }
    
    /**
     * Upload a document for a specific contract by a landlord
     * 
     * @param Request $request The request
     * @param Contract $contract The contract to upload a document for
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadContractDocument(Request $request, Contract $contract)
    {
        $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|string|in:id,contract,proof_of_address,other',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'document_description' => 'nullable|string|max:1000',
            'tenant_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'contract_id' => 'required|exists:contracts,id',
        ]);
        
        $user = Auth::user();
        
        // Verify the contract belongs to this landlord
        if ($contract->room->property->landlord_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Verify the tenant matches the contract
        if ($contract->user_id != $request->tenant_id) {
            return back()->with('error', 'Tenant ID does not match the contract.');
        }
        
        // Create uploads directory if it doesn't exist
        $uploadPath = 'uploads/tenant_documents/' . $request->tenant_id;
        if (!file_exists(public_path($uploadPath))) {
            mkdir(public_path($uploadPath), 0777, true);
        }
        
        // Store the file with a unique name
        $fileName = time() . '_' . $request->file('document_file')->getClientOriginalName();
        $request->file('document_file')->move(public_path($uploadPath), $fileName);
        
        // Save document info to database
        $document = new Document();
        $document->user_id = $request->tenant_id;
        $document->room_id = $request->room_id;
        $document->contract_id = $request->contract_id;
        $document->name = $request->document_name;
        $document->type = $request->document_type;
        $document->file_path = $uploadPath . '/' . $fileName;
        $document->description = $request->document_description;
        $document->save();
        
        return redirect()->route('landlord.contracts.show', $contract->id)->with('success', 'Document uploaded successfully.');
    }
    
    /**
     * Delete a document
     * This method allows both tenants and landlords to delete documents
     * 
     * @param Document $document The document to delete
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Document $document)
    {
        $user = Auth::user();
        
        // Check if user has permission to delete this document
        $canDelete = false;
        
        // Tenant can delete their own documents
        if ($user->id === $document->user_id) {
            $canDelete = true;
        }
        
        // Landlord can delete documents from their tenants
        if ($user->hasRole('landlord') && $document->user->landlord_id === $user->id) {
            $canDelete = true;
        }
        
        if (!$canDelete) {
            abort(403, 'Unauthorized access');
        }
        
        // Get the file path
        $filePath = public_path($document->file_path);
        
        // Delete the file if it exists
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Store information about which contract the document was associated with (if any)
        $contractId = $document->contract_id;
        
        // Delete the document record
        $document->delete();
        
        // Redirect based on user role
        if ($user->hasRole('landlord') && $contractId) {
            return redirect()->route('landlord.contracts.show', $contractId)->with('success', 'Document deleted successfully.');
        } else {
            return redirect()->route('tenant.profile')->with('success', 'Document deleted successfully.');
        }
    }
}
