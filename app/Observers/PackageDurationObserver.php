<?php

namespace App\Observers;

use App\Models\PackageDuration;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;


class PackageDurationObserver
{


    /**
     * Ketika durasi dibuat
     */
    public function created(PackageDuration $duration): void
    {

        $this->log(
            'created',
            $duration,
            "Menambahkan durasi paket " .
            ($duration->package?->name ?? '-')
        );

    }



    /**
     * Ketika durasi diperbarui
     */
    public function updated(PackageDuration $duration): void
    {

        $this->log(
            'updated',
            $duration,
            "Mengubah durasi paket " .
            ($duration->package?->name ?? '-')
        );

    }



    /**
     * Ketika durasi dihapus
     */
    public function deleted(PackageDuration $duration): void
    {

        $this->log(
            'deleted',
            $duration,
            "Menghapus durasi paket " .
            ($duration->package?->name ?? '-')
        );

    }



    /**
     * Simpan Audit Log
     */
    private function log(
        string $action,
        PackageDuration $duration,
        string $description
    ): void {


        AuditLog::create([

            'user_id' => Auth::id(),

            'action' => $action,

            'module' => 'Package Duration',

            'model_type' => PackageDuration::class,

            'model_id' => $duration->id,

            'description' => $description,


            'old_values' => $action === 'updated'
                ? $duration->getOriginal()
                : null,


            'new_values' => in_array(
                $action,
                [
                    'created',
                    'updated'
                ]
            )
                ? $duration->getAttributes()
                : null,


            'ip_address' => request()->ip(),

            'user_agent' => request()->userAgent(),

        ]);

    }

}
