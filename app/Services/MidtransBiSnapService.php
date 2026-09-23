<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransBiSnapService
{
    private const ACCESS_TOKEN_PATH = '/v1.0/access-token/b2b';

    /**
     * Obtain a BI-SNAP B2B access token. This call authenticates only; it
     * cannot create a payment, withdrawal, or disbursement.
     *
     * @return array{responseCode?: string, responseMessage?: string, accessToken: string, tokenType?: string, expiresIn?: string}
     */
    public function getB2BAccessToken(): array
    {
        $clientId = $this->requiredConfig('client_id');
        $timestamp = now('Asia/Jakarta')->format('Y-m-d\\TH:i:sP');
        $signature = $this->createAccessTokenSignature($clientId, $timestamp);
        $endpoint = rtrim($this->requiredConfig('base_url'), '/').self::ACCESS_TOKEN_PATH;

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout(15)
                ->withHeaders([
                    'X-TIMESTAMP' => $timestamp,
                    'X-SIGNATURE' => $signature,
                    'X-CLIENT-KEY' => $clientId,
                ])
                ->post($endpoint, ['grantType' => 'client_credentials']);
        } catch (\Throwable $exception) {
            Log::warning('Midtrans BI-SNAP access-token request failed.', [
                'exception' => class_basename($exception),
            ]);

            throw new \RuntimeException('Permintaan access token BI-SNAP gagal dikirim.', previous: $exception);
        }

        $payload = $response->json() ?: [];

        if (! $response->successful() || empty($payload['accessToken'])) {
            Log::warning('Midtrans BI-SNAP access-token request was rejected.', [
                'http_status' => $response->status(),
                'response_code' => $payload['responseCode'] ?? null,
                'reference_no' => $payload['referenceNo'] ?? null,
            ]);

            throw new RequestException($response);
        }

        Log::info('Midtrans BI-SNAP access token obtained.', [
            'response_code' => $payload['responseCode'] ?? null,
            'expires_in' => $payload['expiresIn'] ?? null,
            'token_length' => strlen((string) $payload['accessToken']),
        ]);

        return $payload;
    }

    /**
     * A safe authentication health check that intentionally does not expose
     * the returned bearer token to callers or logs.
     */
    public function testAuthentication(): array
    {
        $token = $this->getB2BAccessToken();

        return [
            'authenticated' => true,
            'response_code' => $token['responseCode'] ?? null,
            'token_type' => $token['tokenType'] ?? null,
            'expires_in' => $token['expiresIn'] ?? null,
        ];
    }

    public function createAccessTokenSignature(string $clientId, string $timestamp): string
    {
        $privateKeyPath = $this->requiredConfig('private_key_path');
        $path = $this->resolvePrivateKeyPath($privateKeyPath);

        if (! is_readable($path)) {
            throw new \RuntimeException('Private key BI-SNAP tidak dapat dibaca.');
        }

        $privateKey = openssl_pkey_get_private((string) file_get_contents($path));

        if ($privateKey === false) {
            throw new \RuntimeException('Private key BI-SNAP tidak valid.');
        }

        $signed = openssl_sign(
            $clientId.'|'.$timestamp,
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );

        if (! $signed) {
            throw new \RuntimeException('Signature BI-SNAP tidak dapat dibuat.');
        }

        return base64_encode($signature);
    }

    private function requiredConfig(string $key): string
    {
        $value = config('services.midtrans_bi_snap.'.$key);

        if (! is_string($value) || trim($value) === '') {
            throw new \RuntimeException("Konfigurasi MIDTRANS_BI_SNAP_{$key} belum diisi.");
        }

        return $value;
    }

    private function resolvePrivateKeyPath(string $path): string
    {
        return str_starts_with($path, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:[\\\\\/]/', $path)
            ? $path
            : base_path($path);
    }
}
