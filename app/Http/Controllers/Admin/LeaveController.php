<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Display a listing of leave applications.
     */
    public function index(Request $request)
    {
        $query = Leave::with('user')->latest('filing_date');

        if ($request->filled('status')) {
            $query->where('workflow_status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        $leaves = $query->paginate(15)->withQueryString();

        $stats = [
            'pending'                => Leave::where('workflow_status', 'pending')->count(),
            'pending_recommendation' => Leave::where('workflow_status', 'pending_recommendation')->count(),
            'approved'               => Leave::where('workflow_status', 'approved')->count(),
            'rejected'               => Leave::where('workflow_status', 'rejected')->count(),
        ];

        $departments = Department::orderBy('name')->pluck('name', 'id');

        return view('admin.leave.index', compact('leaves', 'stats', 'departments'));
    }

    /**
     * Show the form for creating a new leave application.
     */
    public function create()
    {
        return view('admin.leave.create');
    }

    /**
     * Store a newly created leave application.
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.leave.index')
            ->with('error', 'Creating leave applications from the admin panel is not yet available.');
    }

    /**
     * Display the specified leave application.
     */
    public function show(Leave $leave)
    {
        $leave->load('user', 'certifiedBy', 'recommendedBy', 'approvedByPresident', 'approvedBy');
        return view('admin.leave.show', compact('leave'));
    }

    /**
     * Show the leave certification form (Section 7.A).
     */
    public function certify(Leave $leave)
    {
        $leave->load('user');
        return view('admin.leave.certify', compact('leave'));
    }

    /**
     * Store the leave certification.
     */
    public function storeCertification(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'credit_as_of_date' => 'required|date',
            'vacation_earned'   => 'required|numeric|min:0',
            'vacation_less'     => 'required|numeric|min:0',
            'vacation_balance'  => 'required|numeric',
            'sick_earned'       => 'required|numeric|min:0',
            'sick_less'         => 'required|numeric|min:0',
            'sick_balance'      => 'required|numeric',
        ]);

        $leave->update(array_merge($validated, [
            'certified_by'    => Auth::id(),
            'certified_at'    => now(),
            'workflow_status' => 'pending_recommendation',
            'status'          => 'pending_recommendation',
        ]));

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave credits certified. Application is now pending supervisor recommendation.');
    }

    /**
     * Show the form for editing a leave application.
     */
    public function edit(Leave $leave)
    {
        return view('admin.leave.edit', compact('leave'));
    }

    /**
     * Update the specified leave application.
     */
    public function update(Request $request, Leave $leave)
    {
        return redirect()->route('admin.leave.index')
            ->with('error', 'Editing leave applications from the admin panel is not yet available.');
    }

    /**
     * Approve a leave application.
     */
    public function approve(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'approved_for'     => 'required|in:with_pay,without_pay,others',
            'with_pay_days'    => 'nullable|numeric|min:0',
            'without_pay_days' => 'nullable|numeric|min:0',
            'others_specify'   => 'nullable|string|max:255',
            'admin_notes'      => 'nullable|string',
        ]);

        $leave->update(array_merge($validated, [
            'approved_by'     => Auth::id(),
            'approved_at'     => now(),
            'workflow_status' => 'approved',
            'status'          => 'approved',
        ]));

        return redirect()->route('admin.leave.show', $leave)
            ->with('success', 'Leave application approved successfully.');
    }

    /**
     * Reject a leave application.
     */
    public function reject(Request $request, Leave $leave)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leave->update([
            'rejection_reason' => $request->rejection_reason,
            'workflow_status'  => 'rejected',
            'status'           => 'rejected',
        ]);

        return redirect()->route('admin.leave.show', $leave)
            ->with('success', 'Leave application rejected.');
    }

    /**
     * Cancel a leave application.
     */
    public function cancel(Request $request, Leave $leave)
    {
        $leave->update([
            'workflow_status' => 'cancelled',
            'status'          => 'cancelled',
        ]);

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave application cancelled.');
    }

    /**
     * Remove the specified leave application.
     */
    public function destroy(Leave $leave)
    {
        $leave->delete();

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave application deleted.');
    }

    /**
     * Export leave applications.
     */
    public function export(Request $request)
    {
        return redirect()->back()->with('error', 'Leave export is not yet available.');
    }

    /**
     * Show leave report.
     */
    public function report(Request $request)
    {
        return redirect()->back()->with('error', 'Leave report is not yet available.');
    }

    /**
     * Download the leave PDF.
     */
    public function downloadPdf(Leave $leave)
    {
        return redirect()->back()->with('error', 'PDF download is not yet available.');
    }

    /**
     * View the leave PDF inline.
     */
    public function viewPdf(Leave $leave)
    {
        return redirect()->back()->with('error', 'PDF viewer is not yet available.');
    }
}
