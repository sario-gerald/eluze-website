<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditTrailController extends Controller
{
    /**
     * Display recent admin activity.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'action' => ['nullable', 'string', 'max:80'],
        ]);

        $activeAction = $validated['action'] ?? null;

        $logs = AuditLog::query()
            ->with(['user', 'subject'])
            ->when($activeAction, fn ($query) => $query->where('action', $activeAction))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.audit-trail.index', [
            'activeAction' => $activeAction,
            'actions' => AuditLog::query()
                ->select('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action'),
            'logs' => $logs,
            'totalLogs' => AuditLog::query()->count(),
        ]);
    }
}
