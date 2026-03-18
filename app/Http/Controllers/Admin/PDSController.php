<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\PDS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PDSController extends Controller
{
    /**
     * Display a listing of PDS records.
     */
    public function index(Request $request)
    {
        $query = PDS::with(['employee.department', 'employee.position', 'lastActionBy'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        $pdsList = $query->paginate(15)->withQueryString();

        $stats = [
            'total'        => PDS::count(),
            'submitted'    => PDS::where('status', PDS::STATUS_SUBMITTED)->count(),
            'under_review' => PDS::where('status', PDS::STATUS_UNDER_REVIEW)->count(),
            'verified'     => PDS::where('status', PDS::STATUS_VERIFIED)->count(),
            'rejected'     => PDS::where('status', PDS::STATUS_REJECTED)->count(),
        ];

        $departments = Department::orderBy('name')->pluck('name', 'id');
        $statuses    = PDS::getStatuses();

        return view('admin.pds.index', compact('pdsList', 'stats', 'departments', 'statuses'));
    }

    /**
     * Show the form for creating a new PDS entry.
     */
    public function create()
    {
        $employees = Employee::with('department')->orderBy('last_name')->get();
        return view('admin.pds.create', compact('employees'));
    }

    /**
     * Store a newly created PDS record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:tbl_employee,id'],
        ]);

        // Prevent duplicate PDS per employee
        $existing = PDS::where('employee_id', $request->employee_id)->first();
        if ($existing) {
            return redirect()->route('admin.pds.show-detail', $existing)
                ->with('info', 'A PDS record already exists for this employee.');
        }

        $pds = PDS::create([
            'employee_id' => $request->employee_id,
            'status'      => PDS::STATUS_INCOMPLETE,
        ]);

        return redirect()->route('admin.pds.show-detail', $pds)
            ->with('success', 'PDS record created successfully.');
    }

    /**
     * Display a PDS record.
     * Also handles the /show route (no ID) by redirecting to index.
     */
    public function show(PDS $pds = null)
    {
        if (!$pds || !$pds->exists) {
            return redirect()->route('admin.pds.index');
        }

        $pds->load(['employee.department', 'employee.position', 'lastActionBy']);
        $statuses = PDS::getStatuses();

        return view('admin.pds.show', compact('pds', 'statuses'));
    }

    /**
     * Show the form for editing a PDS record.
     */
    public function edit(PDS $pds)
    {
        $pds->load('employee.department');
        return view('admin.pds.edit', compact('pds'));
    }

    /**
     * Update a PDS record.
     */
    public function update(Request $request, PDS $pds)
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(PDS::getStatuses()))],
        ]);

        $pds->update([
            'status'          => $request->status,
            'last_action_by'  => Auth::id(),
            'last_action_at'  => now(),
        ]);

        return redirect()->route('admin.pds.show-detail', $pds)
            ->with('success', 'PDS record updated.');
    }

    /**
     * Delete a PDS record.
     */
    public function destroy(PDS $pds)
    {
        $pds->delete();

        return redirect()->route('admin.pds.index')
            ->with('success', 'PDS record deleted.');
    }

    /**
     * Mark a PDS as under review.
     */
    public function markUnderReview(Request $request, PDS $pds)
    {
        $pds->markUnderReview(Auth::user());

        return redirect()->route('admin.pds.show-detail', $pds)
            ->with('success', 'PDS marked as under review.');
    }

    /**
     * Verify a PDS record.
     */
    public function verify(Request $request, PDS $pds)
    {
        $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $pds->verify(Auth::user(), $request->remarks);

        return redirect()->route('admin.pds.show-detail', $pds)
            ->with('success', 'PDS verified successfully.');
    }

    /**
     * Reject a PDS record.
     */
    public function reject(Request $request, PDS $pds)
    {
        $request->validate([
            'remarks' => ['required', 'string', 'max:1000'],
        ]);

        $pds->reject(Auth::user(), $request->remarks);

        return redirect()->route('admin.pds.show-detail', $pds)
            ->with('success', 'PDS rejected.');
    }

    /**
     * Filter PDS by status — redirects to index with filter applied.
     */
    public function filterByStatus(Request $request)
    {
        return redirect()->route('admin.pds.index', [
            'status' => $request->status,
        ]);
    }

    /**
     * Export PDS records.
     */
    public function export(Request $request)
    {
        // Placeholder until an export class is created
        return redirect()->route('admin.pds.index')
            ->with('info', 'PDS export is not yet available.');
    }

    /**
     * Get PDS records for a specific employee.
     */
    public function getByEmployee(Request $request, $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $pds = PDS::with('lastActionBy')
            ->where('employee_id', $employeeId)
            ->latest()
            ->get();

        return response()->json([
            'employee' => $employee,
            'pds'      => $pds,
        ]);
    }
}
