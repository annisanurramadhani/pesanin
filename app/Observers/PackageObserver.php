<?php

namespace App\Observers;

use App\Models\Package;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;


class PackageObserver
{

    /**
     * Ketika package dibuat
     */
    public function created(Package $package): void
    {
        $this->log(
            'created',
            $package,
            "Menambahkan paket baru: {$package->name}"
        );
    }



    /**
     * Ketika package diperbarui
     */
    public function updated(Package $package): void
    {
        $this->log(
            'updated',
            $package,
            "Mengubah data paket: {$package->name}"
        );
    }



    /**
     * Ketika package dihapus
     */
    public function deleted(Package $package): void
    {
        $this->log(
            'deleted',
            $package,
            "Menghapus paket: {$package->name}"
        );
    }



    /**
     * Simpan Audit Log
     */
    private function log(
        string $action,
        Package $package,
        string $description
    ): void {


        AuditLog::create([

            'user_id' => Auth::id(),

            'action' => $action,

            'module' => 'Package',

            'model_type' => Package::class,

            'model_id' => $package->id,

            'description' => $description,


            'old_values' => $action === 'updated'
                ? $package->getOriginal()
                : null,


            'new_values' => in_array(
                $action,
                [
                    'created',
                    'updated'
                ]
            )
                ? $package->getAttributes()
                : null,


            'ip_address' => request()->ip(),

            'user_agent' => request()->userAgent(),

        ]);

    }

}
