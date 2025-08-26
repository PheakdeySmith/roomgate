<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    protected $exchangeRateService;
    
    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }
    
    public function index()
    {
        $currencies = $this->exchangeRateService->getSupportedCurrencies();
        return view('backends.dashboard.landlord.profile', compact('currencies'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        // Handle profile image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $relativePath = 'uploads/profile-photos/' . $imageName;
            $uploadsDir = public_path('uploads/profile-photos');
            if (!File::isDirectory($uploadsDir)) {
                File::makeDirectory($uploadsDir, 0755, true);
            }
            // Delete old image if it exists
            if ($user->image && File::exists(public_path($user->image))) {
                File::delete(public_path($user->image));
            }
            // Save new image
            $image->move($uploadsDir, $imageName);
            $data['image'] = $relativePath;
        }

        $user->update($data);

        return redirect()->route('landlord.profile.index')->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('landlord.profile.index')->with('success', 'Password updated successfully!');
    }

    public function updateCurrencySettings(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'currency_code' => ['required', 'string', 'max:3'],
            'exchange_rate' => ['required', 'numeric', 'min:0.01'],
        ]);
        
        try {
            $currencyCode = strtoupper($request->currency_code);
            $exchangeRate = round($request->exchange_rate, 2); // Round to 2 decimal places
            
            // Log the update for debugging
            \Log::info('Updating currency settings', [
                'user_id' => $user->id,
                'old_currency' => $user->currency_code,
                'new_currency' => $currencyCode,
                'old_rate' => $user->exchange_rate,
                'new_rate' => $exchangeRate,
                'request_data' => $request->all()
            ]);
            
            // If the user is switching to a new currency and the exchange rate is the default,
            // try to get the current rate from the API
            if ($user->currency_code !== $currencyCode && $exchangeRate == 1.0 && $currencyCode !== 'USD') {
                $fetchedRate = $this->exchangeRateService->getExchangeRate($currencyCode, true); // Force API fetch
                if ($fetchedRate !== null) {
                    \Log::info("User switched to $currencyCode, fetched new rate: $fetchedRate");
                    $exchangeRate = $fetchedRate;
                }
            }
            
            $user->currency_code = $currencyCode;
            $user->exchange_rate = $exchangeRate;
            $user->save();
            
            return redirect()->route('landlord.profile.index')->with('success', 'Currency settings updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to update currency settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update currency settings: ' . $e->getMessage());
        }
    }
    
    /**
     * AJAX endpoint to fetch current exchange rate
     */
    public function fetchExchangeRate(Request $request)
    {
        $request->validate([
            'currency_code' => ['required', 'string', 'size:3'],
            'force_refresh' => ['nullable', 'boolean'],
        ]);
        
        $currencyCode = strtoupper($request->currency_code);
        $forceRefresh = $request->has('force_refresh') ? $request->force_refresh : false;
        
        // Store original values to determine source later
        $isStoredPreference = false;
        $originalUserRate = null;
        
        if (auth()->check() && !$forceRefresh) {
            $user = auth()->user();
            if ($user->currency_code === $currencyCode && $user->exchange_rate > 0) {
                $originalUserRate = $user->exchange_rate;
                \Log::info("User has a stored preference for $currencyCode: $originalUserRate");
            }
        }
        
        // Get rate - if forceRefresh is true, ignore user's stored preference
        $rate = $this->exchangeRateService->getExchangeRate($currencyCode, $forceRefresh);
        
        if ($rate === null) {
            return response()->json([
                'success' => false,
                'message' => "Could not fetch exchange rate for $currencyCode"
            ], 404);
        }
        
        // Round to 2 decimal places
        $rate = round($rate, 2);
        
        // Determine if this is from user's stored preference
        if ($originalUserRate !== null && abs($originalUserRate - $rate) < 0.01) {
            $isStoredPreference = true;
            \Log::info("Using stored preference for $currencyCode: $rate");
        } else {
            \Log::info("Using API rate for $currencyCode: $rate");
        }
        
        return response()->json([
            'success' => true,
            'currency' => $currencyCode,
            'rate' => $rate,
            'is_stored_preference' => $isStoredPreference,
            'source' => $isStoredPreference ? 'user_preference' : 'api'
        ]);
    }
    
    /**
     * AJAX endpoint to format money values according to the user's currency preference
     */
    public function getFormattedMoney(Request $request)
    {
        $request->validate([
            'amounts' => ['required', 'array'],
            'amounts.*' => ['numeric'],
        ]);
        
        $amounts = $request->amounts;
        $formatted = [];
        
        foreach ($amounts as $amount) {
            // Ensure the amount is properly cast to float
            $numericAmount = is_numeric($amount) ? (float)$amount : 0;
            $formatted[] = format_money($numericAmount);
        }
        
        return response()->json([
            'success' => true,
            'formatted' => $formatted
        ]);
    }
    
    public function updateQRCodes(Request $request)
    {
        $user = Auth::user();

        // Debug information
        \Log::info('QR Code Update Request', [
            'has_qr_code_1' => $request->hasFile('qr_code_1'),
            'has_qr_code_2' => $request->hasFile('qr_code_2'),
            'remove_qr_1' => $request->boolean('remove_qr_1'),
            'remove_qr_2' => $request->boolean('remove_qr_2'),
            'all_files' => $request->allFiles(),
            'all_inputs' => $request->all()
        ]);

        $request->validate([
            'qr_code_1' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'qr_code_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'remove_qr_1' => ['nullable', 'boolean'],
            'remove_qr_2' => ['nullable', 'boolean'],
        ]);

        $data = [];

        // Create QR codes directory if it doesn't exist
        $uploadsDir = public_path('uploads/qrcodes');
        if (!File::isDirectory($uploadsDir)) {
            File::makeDirectory($uploadsDir, 0755, true);
        }

        // Handle QR Code 1
        \Log::info('Processing QR Code 1', [
            'hasFile' => $request->hasFile('qr_code_1'),
            'file_exists' => isset($_FILES['qr_code_1']),
            'file_details' => $request->hasFile('qr_code_1') ? [
                'name' => $request->file('qr_code_1')->getClientOriginalName(),
                'size' => $request->file('qr_code_1')->getSize(),
                'mime' => $request->file('qr_code_1')->getMimeType(),
            ] : 'No file'
        ]);
        
        if ($request->hasFile('qr_code_1')) {
            $qrCode = $request->file('qr_code_1');
            // Generate a simpler, more reliable filename
            $qrCodeName = 'qr1_user_' . $user->id . '_' . time() . '.' . $qrCode->getClientOriginalExtension();
            
            // Delete old QR code if it exists
            if ($user->qr_code_1 && File::exists(public_path('uploads/qrcodes/' . $user->qr_code_1))) {
                File::delete(public_path('uploads/qrcodes/' . $user->qr_code_1));
            }
            
            // Ensure directory exists
            if (!File::isDirectory($uploadsDir)) {
                File::makeDirectory($uploadsDir, 0755, true, true);
            }
            
            try {
                // Save new QR code
                $qrCode->move($uploadsDir, $qrCodeName);
                
                \Log::info('QR Code 1 uploaded', [
                    'path' => $uploadsDir . '/' . $qrCodeName,
                    'exists' => File::exists($uploadsDir . '/' . $qrCodeName)
                ]);
                
                // Make sure the file exists after saving
                if (File::exists($uploadsDir . '/' . $qrCodeName)) {
                    $data['qr_code_1'] = $qrCodeName;
                } else {
                    \Log::error('QR Code 1 file not found after upload');
                    return redirect()->route('landlord.profile.index')
                        ->with('error', 'Failed to upload QR code 1. Please try again.');
                }
            } catch (\Exception $e) {
                \Log::error('QR Code 1 upload error', ['exception' => $e->getMessage()]);
                return redirect()->route('landlord.profile.index')
                    ->with('error', 'Error uploading QR code 1: ' . $e->getMessage());
            }
        } elseif ($request->boolean('remove_qr_1')) {
            // Remove QR code 1 if requested
            if ($user->qr_code_1 && File::exists(public_path('uploads/qrcodes/' . $user->qr_code_1))) {
                File::delete(public_path('uploads/qrcodes/' . $user->qr_code_1));
            }
            $data['qr_code_1'] = null;
        }

        // Handle QR Code 2
        if ($request->hasFile('qr_code_2')) {
            $qrCode = $request->file('qr_code_2');
            // Generate a simpler, more reliable filename
            $qrCodeName = 'qr2_user_' . $user->id . '_' . time() . '.' . $qrCode->getClientOriginalExtension();
            
            // Delete old QR code if it exists
            if ($user->qr_code_2 && File::exists(public_path('uploads/qrcodes/' . $user->qr_code_2))) {
                File::delete(public_path('uploads/qrcodes/' . $user->qr_code_2));
            }
            
            // Ensure directory exists
            if (!File::isDirectory($uploadsDir)) {
                File::makeDirectory($uploadsDir, 0755, true, true);
            }
            
            // Save new QR code
            $qrCode->move($uploadsDir, $qrCodeName);
            
            // Make sure the file exists after saving
            if (File::exists($uploadsDir . '/' . $qrCodeName)) {
                $data['qr_code_2'] = $qrCodeName;
            } else {
                return redirect()->route('landlord.profile.index')
                    ->with('error', 'Failed to upload QR code 2. Please try again.');
            }
        } elseif ($request->boolean('remove_qr_2')) {
            // Remove QR code 2 if requested
            if ($user->qr_code_2 && File::exists(public_path('uploads/qrcodes/' . $user->qr_code_2))) {
                File::delete(public_path('uploads/qrcodes/' . $user->qr_code_2));
            }
            $data['qr_code_2'] = null;
        }

        if (!empty($data)) {
            $user->update($data);
        }

        return redirect()->route('landlord.profile.index')->with('success', 'QR codes updated successfully!');
    }
}
