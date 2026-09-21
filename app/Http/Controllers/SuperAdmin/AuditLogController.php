<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query()
            ->with('user')
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('model_type', 'like', "%{$search}%")
                    ->orWhere('model_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {

                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");

                    });

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Action
        |--------------------------------------------------------------------------
        */

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Model
        |--------------------------------------------------------------------------
        */

        if ($request->filled('model')) {
            $query->where('model_type', $request->model);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Date
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Logs
        |--------------------------------------------------------------------------
        */

        $auditLogs = $query
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Action Options
        |--------------------------------------------------------------------------
        */

        $actions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        /*
        |--------------------------------------------------------------------------
        | Model Options
        |--------------------------------------------------------------------------
        */

        $models = AuditLog::query()
            ->whereNotNull('model_type')
            ->select('model_type')
            ->distinct()
            ->orderBy('model_type')
            ->pluck('model_type');

        return view(
            'super_admin.audit_logs.index',
            compact(
                'auditLogs',
                'actions',
                'models'
            )
        );
    }
}
