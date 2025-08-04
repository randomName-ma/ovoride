<?php


namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class SmsSyrianService
{
    protected $client;
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('SMS_TOKEN');
        $this->apiUrl = "https://www.traccar.org/sms/";
    }

    public function SendtoUser($user,$mobile=null): void
    {
        if($mobile==null) $mobile=$user->mobile;

        $user->ver_code         = verificationCode(6);
        $user->ver_code_send_at = Carbon::now();
        $user->save();
        $phone = $this->normalizeSyrianPhoneNumber($mobile); // <-- Normalize before use
        $this->sendSMS($phone,$user->ver_code,'your code in mishwar is');
    }

    public function sendSMS($phone, $otp, $message)
    {

        \Log::info($phone);
        $requestBody = [
            'message' => sprintf('%s %d', $message, $otp),
            'to' => $phone,
        ];

        try {
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $requestBody,
            ]);
            \Log::info($response->getBody());
            return $response->getBody()->getContents();
        } catch (RequestException $e) {
            throw new \Exception('Failed to send SMS: ' . $e->getMessage());
        }
    }
    protected function normalizeSyrianPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters except the plus sign
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Normalize the number
        if (preg_match('/^\+9639\d{8}$/', $phone)) {
            return $phone;
        } elseif (preg_match('/^09\d{8}$/', $phone)) {
            return '+963' . substr($phone, 1);
        } elseif (preg_match('/^9\d{8}$/', $phone)) {
            return '+963' . $phone;
        } elseif (preg_match('/^9639\d{8}$/', $phone)) {
            return '+' . $phone;
        }

        // If it's invalid, throw an exception
        throw new \InvalidArgumentException("رقم الهاتف غير صالح: $phone");
    }
}
