<?php

namespace Tests\Feature;

use App\Services\MidtransBiSnapService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransBiSnapServiceTest extends TestCase
{
    private string $privateKeyPath;

    private string $publicKey;

    protected function setUp(): void
    {
        parent::setUp();

        $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        openssl_pkey_export($key, $privateKey);
        $this->publicKey = openssl_pkey_get_details($key)['key'];
        $this->privateKeyPath = tempnam(sys_get_temp_dir(), 'pesanin-bi-snap-');
        file_put_contents($this->privateKeyPath, $privateKey);

        config([
            'services.midtrans_bi_snap.base_url' => 'https://merchants.sbx.midtrans.com',
            'services.midtrans_bi_snap.client_id' => 'test-bi-snap-client-id',
            'services.midtrans_bi_snap.private_key_path' => $this->privateKeyPath,
        ]);
    }

    protected function tearDown(): void
    {
        if (isset($this->privateKeyPath) && is_file($this->privateKeyPath)) {
            unlink($this->privateKeyPath);
        }

        parent::tearDown();
    }

    public function test_it_requests_a_b2b_access_token_with_a_valid_rsa_signature(): void
    {
        Http::fake([
            'https://merchants.sbx.midtrans.com/v1.0/access-token/b2b' => Http::response([
                'responseCode' => '2007300',
                'responseMessage' => 'Successful',
                'accessToken' => 'mock-access-token',
                'tokenType' => 'Bearer',
                'expiresIn' => '900',
            ]),
        ]);

        $result = app(MidtransBiSnapService::class)->testAuthentication();

        $this->assertSame([
            'authenticated' => true,
            'response_code' => '2007300',
            'token_type' => 'Bearer',
            'expires_in' => '900',
        ], $result);

        Http::assertSent(function ($request) {
            $timestamp = $request->header('X-TIMESTAMP')[0] ?? '';
            $signature = base64_decode($request->header('X-SIGNATURE')[0] ?? '', true);

            return $request->url() === 'https://merchants.sbx.midtrans.com/v1.0/access-token/b2b'
                && $request['grantType'] === 'client_credentials'
                && $request->header('X-CLIENT-KEY')[0] === 'test-bi-snap-client-id'
                && $signature !== false
                && openssl_verify('test-bi-snap-client-id|'.$timestamp, $signature, $this->publicKey, OPENSSL_ALGO_SHA256) === 1;
        });
    }
}
