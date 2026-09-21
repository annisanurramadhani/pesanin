<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;


class AuditLogger
{

    public function log(
        string $action,
        $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ) {

        AuditLog::create([

            'user_id' => Auth::id(),


            'action' => $action,


            'model_type' => $model
                ? get_class($model)
                : null,


            'model_id' => $model?->id,


            'old_values' => $oldValues,


            'new_values' => $newValues,


            'ip_address' => request()->ip(),


            'user_agent' => request()->userAgent(),

        ]);

    }

}