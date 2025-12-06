<?php

namespace App\Http\Controllers;

use App\Models\PayrollSetting;
use App\Models\DeductionType;
use App\Models\Deduction;
use Illuminate\Http\Request;

class PayrollSettingController extends Controller
{
    public function index(Request $request)
    {
        $settings = PayrollSetting::all()->groupBy('group');
        $deductionTypes = DeductionType::withCount('deductions')->orderBy('name')->get();
        
        // Deductions Logic
        $query = Deduction::with('deductionType');
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->type) {
            $query->where('deduction_type_id', $request->type);
        }
        $deductions = $query->orderBy('name')->paginate(10);

        return view('admin.payroll.settings.index', compact('settings', 'deductionTypes', 'deductions'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            PayrollSetting::where('key', $key)->update(['value' => $value]);
        }

        return redirect()->back()->with('success', 'Payroll settings updated successfully.');
    }
}
