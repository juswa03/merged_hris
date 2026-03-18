<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSetting;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $settings = MaintenanceSetting::settings();
        return view('admin.maintenance.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title'             => 'required|string|max:200',
            'message'           => 'required|string',
            'whitelisted_ips'   => 'nullable|string',
            'scheduled_end_at'  => 'nullable|date|after:now',
        ]);

        $settings = MaintenanceSetting::settings();
        $settings->update([
            'title'            => $request->title,
            'message'          => $request->message,
            'whitelisted_ips'  => $request->whitelisted_ips,
            'scheduled_end_at' => $request->scheduled_end_at ?: null,
            'activated_by'     => auth()->id(),
        ]);

        return back()->with('success', 'Maintenance settings updated.');
    }

    public function toggle(Request $request)
    {
        $settings = MaintenanceSetting::settings();

        $newState = ! $settings->is_active;
        $settings->update([
            'is_active'    => $newState,
            'activated_by' => auth()->id(),
        ]);

        $label = $newState ? 'enabled' : 'disabled';
        return back()->with('success', "Maintenance mode has been {$label}.");
    }
}
