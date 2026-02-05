<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class SmsService
{
    public function send($to, $message)
    {
        $response = Http::post('https://api.semaphore.co/api/v4/messages', [
            'apikey' => env('SEMAPHORE_API_KEY'),
            'number' => $to,
            'message' => $message,
            'sendername' => env('SEMAPHORE_SENDER'),
        ]);

        return $response->json();
    }
}
