<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelAuthority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TravelController extends Controller
{
    /**
     * Display a listing of travel authorities.
     */
    public function index(Request $request)
    {
        $query = TravelAuthority::with(['employee.user'])->latest('submitted_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('travel_type')) {
            $query->where('travel_type', $request->travel_type);
        }

        $travels = $query->paginate(15)->withQueryString();

        $stats = [
            'pending'   => TravelAuthority::where('status', TravelAuthority::STATUS_PENDING)->count(),
            'approved'  => TravelAuthority::where('status', TravelAuthority::STATUS_APPROVED)->count(),
            'rejected'  => TravelAuthority::where('status', TravelAuthority::STATUS_REJECTED)->count(),
            'completed' => TravelAuthority::where('status', TravelAuthority::STATUS_COMPLETED)->count(),
        ];

        return view('admin.travel.index', compact('travels', 'stats'));
    }

    /**
     * Show the form for creating a new travel authority.
     */
    public function create()
    {
        return view('admin.travel.create');
    }

    /**
     * Store a newly created travel authority.
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.travel.index')
            ->with('error', 'Creating travel authorities from the admin panel is not yet available.');
    }

    /**
     * Display the specified travel authority.
     */
    public function show(TravelAuthority $travel)
    {
        $travel->load(['employee.user', 'approvals', 'submittedBy', 'recommendingOfficial']);
        return view('admin.travel.show', compact('travel'));
    }

    /**
     * Show the form for editing a travel authority.
     */
    public function edit(TravelAuthority $travel)
    {
        return view('admin.travel.edit', compact('travel'));
    }

    /**
     * Update the specified travel authority.
     */
    public function update(Request $request, TravelAuthority $travel)
    {
        return redirect()->route('admin.travel.index')
            ->with('error', 'Editing travel authorities from the admin panel is not yet available.');
    }

    /**
     * Remove the specified travel authority.
     */
    public function destroy(TravelAuthority $travel)
    {
        $travel->delete();

        return redirect()->route('admin.travel.index')
            ->with('success', 'Travel authority deleted.');
    }

    /**
     * Approve the specified travel authority.
     */
    public function approve(Request $request, TravelAuthority $travel)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        $travel->update([
            'status'   => TravelAuthority::STATUS_APPROVED,
            'remarks'  => $request->remarks,
        ]);

        return redirect()->route('admin.travel.show', $travel)
            ->with('success', 'Travel authority approved.');
    }

    /**
     * Reject the specified travel authority.
     */
    public function reject(Request $request, TravelAuthority $travel)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $travel->update([
            'status'           => TravelAuthority::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('admin.travel.show', $travel)
            ->with('success', 'Travel authority rejected.');
    }

    /**
     * Mark the travel authority as completed.
     */
    public function markCompleted(TravelAuthority $travel)
    {
        $travel->update([
            'status'       => TravelAuthority::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        return redirect()->route('admin.travel.show', $travel)
            ->with('success', 'Travel authority marked as completed.');
    }

    /**
     * Filter travel authorities by status.
     */
    public function filterByStatus(Request $request)
    {
        return redirect()->route('admin.travel.index', ['status' => $request->status]);
    }

    /**
     * Export travel authorities.
     */
    public function export(Request $request)
    {
        return redirect()->back()->with('error', 'Travel authority export is not yet available.');
    }

    /**
     * Get travel authorities by employee.
     */
    public function getByEmployee($employeeId)
    {
        $travels = TravelAuthority::where('employee_id', $employeeId)
            ->latest()->get();

        return response()->json($travels);
    }
}
