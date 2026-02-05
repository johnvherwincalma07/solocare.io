<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SMSController extends Controller
{
    public function send(Request $request)
    {
        // Validate inputs
        $validated = $request->validate([
            'contact_number' => 'required',
            'full_name'      => 'required',
            'reference_no'   => 'required'
        ]);

        $phone        = $validated['contact_number'];
        $name         = $validated['full_name'];
        $referenceNo  = $validated['reference_no'];

        // SMS content
        $message = "Hello $name, your Solo Parent application (Ref: $referenceNo) has been APPROVED. Please wait for further instructions. - SoloCare";

        // Send SMS
        $response = Http::asForm()->post('https://api.semaphore.co/api/v4/messages', [
            'apikey'     => env('SEMAPHORE_API_KEY'),
            'number'     => $phone,
            'message'    => $message,
            'sendername' => env('SEMAPHORE_SENDER', 'SoloCare Support'),
        ]);

        // If success
        if ($response->successful()) {
            return response()->json([
                'success'  => true,
                'message'  => 'SMS sent successfully!',
                'response' => $response->json()
            ]);
        }

        // Extract real error safely
        $errorBody = null;

        try {
            $errorBody = $response->json();
        } catch (\Exception $e) {
            $errorBody = $response->body(); // fallback for non-JSON errors
        }
        // Log exact error
        Log::error("Semaphore SMS Error", [
            'status' => $response->status(),
            'error'  => $errorBody
        ]);


        return response()->json([
            'success' => false,
            'message' => 'Failed to send SMS.',
            'error'   => $errorBody
        ], 500);
    }
}



