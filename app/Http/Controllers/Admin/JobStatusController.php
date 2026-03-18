<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobStatus;
use Illuminate\Http\Request;

class JobStatusController extends Controller
{
    public function index()
    {
        $jobStatuses = JobStatus::withCount('employees')->orderBy('name')->paginate(20);

        $stats = [
            'total'   => JobStatus::count(),
            'in_use'  => JobStatus::has('employees')->count(),
            'unused'  => JobStatus::doesntHave('employees')->count(),
        ];

        return view('admin.job-statuses.index', compact('jobStatuses', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:tbl_job_statuses,name',
        ]);

        JobStatus::create(['name' => $request->name]);

        return back()->with('success', 'Job status "' . $request->name . '" created successfully.');
    }

    public function update(Request $request, JobStatus $jobStatus)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:tbl_job_statuses,name,' . $jobStatus->id,
        ]);

        $jobStatus->update(['name' => $request->name]);

        return back()->with('success', 'Job status updated successfully.');
    }

    public function destroy(JobStatus $jobStatus)
    {
        $count = $jobStatus->employees()->count();

        if ($count > 0) {
            return back()->with('error', "Cannot delete \"{$jobStatus->name}\": it is assigned to {$count} employee(s).");
        }

        $jobStatus->delete();

        return back()->with('success', 'Job status deleted.');
    }
}
