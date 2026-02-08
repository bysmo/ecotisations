<?php

namespace App\Services;

use App\Models\SmsGateway;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public const TTL = 600;
    public const LENGTH = 6;

    public function generateAndStore(string $phoneNormalized): string
    {
        $code = $this->generateCode();
        $key = $this->cacheKey($phoneNormalized);
        Cache::put($key, $code, self::TTL);
        return $code;
    }

    public function verify(string $phoneNormalized, string $code): bool
    {
        $key = $this->cacheKey($phoneNormalized);
        $stored = Cache::get($key);
        if ($stored === null) {
            return false;
        }
        $valid = hash_equals((string) $stored, (string) $code);
        if ($valid) {
            Cache::forget($key);
        }
        return $valid;
    }

    /**
     * Envoie le code OTP au numéro via la passerelle SMS active (Paramètres > SMS).
     */
    public function sendOtp(string $phoneNormalized, string $code): bool
    {
        $gateway = SmsGateway::getActive();

        if (!$gateway) {
            Log::channel('single')->info('OTP Membre (aucune passerelle active)', [
                'phone' => $phoneNormalized,
                'code' => $code,
            ]);
            return true;
        }

        $driver = $gateway->code;

        if ($driver === 'log') {
            Log::channel('single')->info('OTP Membre (passerelle Log)', [
                'phone' => $phoneNormalized,
                'code' => $code,
                'expires_in' => self::TTL . 's',
            ]);
            return true;
        }

        if ($driver === 'twilio') {
            return $this->sendViaTwilio($gateway, $phoneNormalized, $code);
        }
        if ($driver === 'vonage') {
            return $this->sendViaVonage($gateway, $phoneNormalized, $code);
        }
        if ($driver === 'africas_talking') {
            return $this->sendViaAfricasTalking($gateway, $phoneNormalized, $code);
        }
        if ($driver === 'infobip') {
            return $this->sendViaInfobip($gateway, $phoneNormalized, $code);
        }
        if ($driver === 'messagebird') {
            return $this->sendViaMessageBird($gateway, $phoneNormalized, $code);
        }

        Log::warning('OTP: passerelle non gérée', ['driver' => $driver]);
        return false;
    }

    protected function generateCode(): string
    {
        $min = (int) str_pad('1', self::LENGTH, '0');
        $max = (int) str_repeat('9', self::LENGTH);
        return (string) random_int($min, $max);
    }

    protected function cacheKey(string $phoneNormalized): string
    {
        return 'membre_otp_' . preg_replace('/\D/', '', $phoneNormalized);
    }

    protected function messageBody(string $code): string
    {
        return "Votre code de vérification : {$code}. Valide 10 minutes. Ne partagez pas ce code.";
    }

    protected function sendViaTwilio(SmsGateway $gateway, string $phoneE164, string $code): bool
    {
        $sid = $gateway->getConfig('account_sid');
        $token = $gateway->getConfig('auth_token');
        $from = $gateway->getConfig('from');
        if (!$sid || !$token || !$from) {
            Log::warning('OTP Twilio: configuration incomplète');
            return false;
        }
        try {
            if (!class_exists(\Twilio\Rest\Client::class)) {
                Log::warning('OTP Twilio: package twilio/sdk non installé');
                return false;
            }
            $client = new \Twilio\Rest\Client($sid, $token);
            $client->messages->create(
                '+' . $phoneE164,
                [
                    'from' => $from,
                    'body' => $this->messageBody($code),
                ]
            );
            return true;
        } catch (\Throwable $e) {
            Log::error('OTP Twilio: ' . $e->getMessage());
            return false;
        }
    }

    protected function sendViaVonage(SmsGateway $gateway, string $phoneE164, string $code): bool
    {
        $apiKey = $gateway->getConfig('api_key');
        $apiSecret = $gateway->getConfig('api_secret');
        $from = $gateway->getConfig('from');
        if (!$apiKey || !$apiSecret || !$from) {
            Log::warning('OTP Vonage: configuration incomplète');
            return false;
        }
        try {
            $response = Http::asForm()->post('https://rest.nexmo.com/sms/json', [
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
                'from' => $from,
                'to' => $phoneE164,
                'text' => $this->messageBody($code),
            ]);
            $data = $response->json();
            $messages = $data['messages'] ?? [];
            $status = $messages[0]['status'] ?? 'unknown';
            if ($status !== '0') {
                Log::error('OTP Vonage: ' . ($messages[0]['error-text'] ?? $status));
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('OTP Vonage: ' . $e->getMessage());
            return false;
        }
    }

    protected function sendViaAfricasTalking(SmsGateway $gateway, string $phoneE164, string $code): bool
    {
        $username = $gateway->getConfig('username');
        $apiKey = $gateway->getConfig('api_key');
        $from = $gateway->getConfig('from') ?: 'OTP';
        if (!$username || !$apiKey) {
            Log::warning('OTP Africa\'s Talking: configuration incomplète');
            return false;
        }
        try {
            $response = Http::withBasicAuth($username, $apiKey)
                ->post('https://api.africastalking.com/version1/messaging', [
                    'username' => $username,
                    'to' => '+' . $phoneE164,
                    'message' => $this->messageBody($code),
                    'from' => $from,
                ]);
            if (!$response->successful()) {
                Log::error('OTP Africa\'s Talking: ' . $response->body());
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('OTP Africa\'s Talking: ' . $e->getMessage());
            return false;
        }
    }

    protected function sendViaInfobip(SmsGateway $gateway, string $phoneE164, string $code): bool
    {
        $baseUrl = rtrim($gateway->getConfig('base_url'), '/');
        $apiKey = $gateway->getConfig('api_key');
        $from = $gateway->getConfig('from');
        if (!$baseUrl || !$apiKey || !$from) {
            Log::warning('OTP Infobip: configuration incomplète');
            return false;
        }
        try {
            $response = Http::withHeaders([
                'Authorization' => 'App ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/sms/2/text/single', [
                'from' => $from,
                'to' => '+' . $phoneE164,
                'text' => $this->messageBody($code),
            ]);
            if (!$response->successful()) {
                Log::error('OTP Infobip: ' . $response->body());
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('OTP Infobip: ' . $e->getMessage());
            return false;
        }
    }

    protected function sendViaMessageBird(SmsGateway $gateway, string $phoneE164, string $code): bool
    {
        $apiKey = $gateway->getConfig('api_key');
        $originator = $gateway->getConfig('originator');
        if (!$apiKey || !$originator) {
            Log::warning('OTP MessageBird: configuration incomplète');
            return false;
        }
        try {
            $response = Http::withHeaders([
                'Authorization' => 'AccessKey ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://rest.messagebird.com/messages', [
                'originator' => $originator,
                'recipients' => [$phoneE164],
                'body' => $this->messageBody($code),
            ]);
            if (!$response->successful()) {
                Log::error('OTP MessageBird: ' . $response->body());
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('OTP MessageBird: ' . $e->getMessage());
            return false;
        }
    }
}
