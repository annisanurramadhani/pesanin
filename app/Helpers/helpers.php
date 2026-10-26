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
        if (empty($image)) {
            return asset('assets/images/menu-default.jpg');
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        if (str_starts_with($image, 'assets/')) {
            return asset($image);
        }

        $s3Url = \Illuminate\Support\Facades\Storage::disk('s3_public')
            ->url($image);

        try {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($image)) {
                return asset('storage/' . $image);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $s3Url;
    }
}

/*
|--------------------------------------------------------------------------
| s3 private storage helper
|--------------------------------------------------------------------------
*/
if (!function_exists('storePrivateFile')) {
    function storePrivateFile(
        \Illuminate\Http\UploadedFile $file,
        string $directory
    ): string {
        $path = $file->store($directory, 's3_private');

        if (!$path) {
            throw new \RuntimeException(
                'Gagal menyimpan dokumen ke penyimpanan privat.'
            );
        }

        return $path;
    }
}

if (!function_exists('deletePrivateFile')) {
    function deletePrivateFile(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        try {
            $localDisk = \Illuminate\Support\Facades\Storage::disk('local');

            // File lama yang tersimpan secara lokal.
            if ($localDisk->exists($path)) {
                $localDisk->delete($path);
                return;
            }

            // File yang tersimpan di S3 private.
            \Illuminate\Support\Facades\Storage::disk('s3_private')
                ->delete($path);
        } catch (\Throwable $e) {
            report($e);
        }
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
