<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportRequest;

class ContactController extends Controller
{
    /**
     * Display the contact support form
     */
    public function index()
    {
        return view('backends.contact.index');
    }
    
    /**
     * Send the contact support email
     */
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        // In a real application, you'd send an email here
        // Mail::to('admin@example.com')->send(new SupportRequest($request->all()));
        
        // For now, just redirect with a success message
        return redirect()->route('contact')->with('success', 'Your message has been sent to support. We will get back to you soon.');
    }
}
