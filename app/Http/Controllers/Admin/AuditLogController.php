<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        if ($request->filled('user_name')) {
            $query->where('user_name', 'like', '%'.$request->user_name.'%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('model_label', 'like', "%{$s}%")
                  ->orWhere('user_name', 'like', "%{$s}%")
                  ->orWhere('model_type', 'like', "%{$s}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $modelTypes = AuditLog::distinct('model_type')
            ->orderBy('model_type')
            ->pluck('model_type')
            ->filter();

        $stats = [
            'total'   => AuditLog::count(),
            'today'   => AuditLog::whereDate('created_at', today())->count(),
            'created' => AuditLog::where('action', 'created')->count(),
            'deleted' => AuditLog::where('action', 'deleted')->count(),
        ];

        return view('admin.audit-logs.index', compact('logs', 'modelTypes', 'stats'));
    }

    public function show(AuditLog $auditLog)
    {
        return view('admin.audit-logs.show', compact('auditLog'));
    }

    public function destroy(AuditLog $auditLog)
    {
        $auditLog->delete();
        return back()->with('success', 'Audit log entry deleted.');
    }

    public function clear(Request $request)
    {
        $request->validate([
            'older_than_days' => 'required|integer|min:7',
        ]);
        $count = AuditLog::where('created_at', '<', now()->subDays($request->older_than_days))->delete();
        return back()->with('success', "Cleared {$count} audit log entries older than {$request->older_than_days} days.");
    }
}
