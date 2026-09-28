<?php

namespace App\Services;

use App\Models\Patient;

class JWTService
{
    /**
     * Generate a JWT token for a patient.
     */
    public static function generateToken(Patient $patient, int $ttlSeconds = 2592000): string
    {
        $secret = config('app.key');
        if (str_starts_with($secret, 'base64:')) {
            $secret = base64_decode(substr($secret, 7));
        }

        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);

        $payload = json_encode([
            'iss' => config('app.url', 'Apex Clinic CRM'),
            'sub' => $patient->id,
            'patient_code' => $patient->patient_id,
            'email' => $patient->email,
            'name' => $patient->full_name,
            'iat' => time(),
            'exp' => time() + $ttlSeconds
        ]);

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Validate a JWT token and return the payload array if valid, or null if invalid/expired.
     */
    public static function validateToken(?string $token): ?array
    {
        if (empty($token)) {
            return null;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;

        $secret = config('app.key');
        if (str_starts_with($secret, 'base64:')) {
            $secret = base64_decode(substr($secret, 7));
        }

        $expectedSignature = self::base64UrlEncode(
            hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true)
        );

        if (!hash_equals($expectedSignature, $base64UrlSignature)) {
            return null;
        }

        $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);
        if (!$payload || !isset($payload['exp']) || time() >= $payload['exp']) {
            return null;
        }

        return $payload;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
