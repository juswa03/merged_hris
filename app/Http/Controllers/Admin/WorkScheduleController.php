<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{
    public function index()
    {
        $schedules = WorkSchedule::withCount('employees')->orderBy('name')->paginate(15);

        $stats = [
            'total'    => WorkSchedule::count(),
            'active'   => WorkSchedule::where('is_active', true)->count(),
            'inactive' => WorkSchedule::where('is_active', false)->count(),
        ];

        return view('admin.work-schedules.index', compact('schedules', 'stats'));
    }

    public function create()
    {
        $days = WorkSchedule::DAYS;
        return view('admin.work-schedules.create', compact('days'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100|unique:tbl_work_schedules,name',
            'type'          => 'required|in:regular,flexible,shift',
            'work_start'    => 'required|date_format:H:i',
            'work_end'      => 'required|date_format:H:i',
            'break_minutes' => 'required|integer|min:0|max:480',
            'working_days'  => 'required|array|min:1',
            'working_days.*'=> 'integer|between:1,7',
            'description'   => 'nullable|string|max:500',
        ]);

        $validated['is_active']    = $request->boolean('is_active', true);
        $validated['working_days'] = array_map('intval', $request->input('working_days', []));

        WorkSchedule::create($validated);

        return redirect()->route('admin.work-schedules.index')
            ->with('success', 'Work schedule "' . $validated['name'] . '" created successfully.');
    }

    public function edit(WorkSchedule $workSchedule)
    {
        $days = WorkSchedule::DAYS;
        return view('admin.work-schedules.edit', compact('workSchedule', 'days'));
    }

    public function update(Request $request, WorkSchedule $workSchedule)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100|unique:tbl_work_schedules,name,' . $workSchedule->id,
            'type'          => 'required|in:regular,flexible,shift',
            'work_start'    => 'required|date_format:H:i',
            'work_end'      => 'required|date_format:H:i',
            'break_minutes' => 'required|integer|min:0|max:480',
            'working_days'  => 'required|array|min:1',
            'working_days.*'=> 'integer|between:1,7',
            'description'   => 'nullable|string|max:500',
        ]);

        $validated['is_active']    = $request->boolean('is_active');
        $validated['working_days'] = array_map('intval', $request->input('working_days', []));

        $workSchedule->update($validated);

        return redirect()->route('admin.work-schedules.index')
            ->with('success', 'Work schedule updated successfully.');
    }

    public function destroy(WorkSchedule $workSchedule)
    {
        $count = $workSchedule->employees()->count();

        if ($count > 0) {
            return back()->with('error', "Cannot delete \"{$workSchedule->name}\": it is assigned to {$count} employee(s).");
        }

        $workSchedule->delete();

        return redirect()->route('admin.work-schedules.index')
            ->with('success', 'Work schedule deleted.');
    }

    public function toggleStatus(WorkSchedule $workSchedule)
    {
        $workSchedule->update(['is_active' => ! $workSchedule->is_active]);

        $status = $workSchedule->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Schedule \"{$workSchedule->name}\" {$status}.");
    }
}
