<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollPeriodController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollPeriod::query();

        // Filter by Year
        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        // Filter by Month
        if ($request->filled('month')) {
            $query->whereMonth('start_date', $request->month);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Cut Off (1st or 2nd half)
        if ($request->filled('cut_off')) {
            if ($request->cut_off == '1st') {
                $query->whereDay('start_date', '<=', 15);
            } elseif ($request->cut_off == '2nd') {
                $query->whereDay('start_date', '>', 15);
            }
        }

        $periods = $query->orderBy('start_date', 'desc')->paginate(10)->withQueryString();
        
        // Get available years for filter dropdown
        $years = PayrollPeriod::selectRaw('YEAR(start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.payroll.periods.index', compact('periods', 'years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'cut_off_type_id' => 'required|integer',
        ]);

        PayrollPeriod::create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'cut_off_type_id' => $request->cut_off_type_id,
            'status' => 'draft',
        ]);

        return redirect()->route('payroll.periods.index')->with('success', 'Payroll period created successfully.');
    }

    public function update(Request $request, PayrollPeriod $period)
    {
        $request->validate([
            'status' => 'required|in:draft,pending,completed',
        ]);

        $period->update([
            'status' => $request->status,
        ]);

        return redirect()->route('payroll.periods.index')->with('success', 'Payroll period status updated successfully.');
    }

    public function destroy(PayrollPeriod $period)
    {
        if ($period->payrolls()->count() > 0) {
            return redirect()->route('payroll.periods.index')->with('error', 'Cannot delete period with existing payroll records.');
        }

        $period->delete();
        return redirect()->route('payroll.periods.index')->with('success', 'Payroll period deleted successfully.');
    }
}
