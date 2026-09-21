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

if (!function_exists('menuImage')) {
    function menuImage($image = null): string
    {
        return $image
            ? asset('storage/' . $image)
            : asset('assets/images/menu-default.jpg');
    }
}


/*
|--------------------------------------------------------------------------
| Audit Log Helper
|--------------------------------------------------------------------------
*/


if (!function_exists('audit')) {

    function audit(
        string $action,
        $model = null,
        ?array $old = null,
        ?array $new = null
    ) {
        return app(\App\Services\AuditLogger::class)
            ->log(
                $action,
                $model,
                $old,
                $new
            );
    }
}
