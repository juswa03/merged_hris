<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;

class OrgChartController extends Controller
{
    public function index()
    {
        $departments = Department::with([
            'employees' => function ($q) {
                $q->with(['position', 'jobStatus'])
                  ->orderBy('last_name');
            },
        ])->orderBy('name')->get();

        // Build department nodes grouped by position
        $nodes = $departments->map(function (Department $dept) {
            $byPosition = $dept->employees->groupBy('position_id');

            $positions = $byPosition->map(function ($emps, $posId) {
                $pos = $emps->first()->position;
                return [
                    'id'        => $posId,
                    'name'      => $pos?->name ?? 'Unassigned',
                    'employees' => $emps->map(fn ($e) => [
                        'id'   => $e->id,
                        'name' => $e->full_name,
                    ])->values(),
                ];
            })->values();

            return [
                'id'             => $dept->id,
                'name'           => $dept->name,
                'employee_count' => $dept->employees->count(),
                'positions'      => $positions,
            ];
        });

        $stats = [
            'departments' => $departments->count(),
            'positions'   => Position::count(),
            'employees'   => Employee::count(),
        ];

        return view('admin.org-chart.index', compact('nodes', 'stats'));
    }
}
