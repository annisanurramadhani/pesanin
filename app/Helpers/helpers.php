<?php

use Illuminate\Support\Facades\Crypt;

if (!function_exists('encryptId')) {
    function encryptId(int $id): string
    {
        return Crypt::encryptString((string) $id);
    }
}

if (!function_exists('decryptId')) {
    function decryptId(string $encryptedId): ?int
    {
        try {
            $id = Crypt::decryptString($encryptedId);

            if (!ctype_digit($id)) {
                return null;
            }

            return (int) $id;
        } catch (\Throwable $e) {
            return null;
        }
    }
}