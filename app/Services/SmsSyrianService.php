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

        $this->sendSMS('+963'.$mobile,$user->ver_code,'your code in mishwar is');
    }

    public function sendSMS($phone, $otp, $message)
    {
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
}
