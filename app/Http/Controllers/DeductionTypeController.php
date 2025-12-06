<?php

namespace App\Http\Controllers;

use App\Models\DeductionType;
use Illuminate\Http\Request;

class DeductionTypeController extends Controller
{
    public function index()
    {
        $deductionTypes = DeductionType::withCount('deductions')->orderBy('name')->paginate(15);
        return view('admin.deductions.types.index', compact('deductionTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tbl_deduction_type,name',
            'description' => 'nullable|string|max:500',
        ]);

        DeductionType::create($request->all());

        return back()->with('success', 'Deduction type created successfully.');
    }

    public function update(Request $request, DeductionType $deductionType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tbl_deduction_type,name,' . $deductionType->id,
            'description' => 'nullable|string|max:500',
        ]);

        $deductionType->update($request->all());

        return back()->with('success', 'Deduction type updated successfully.');
    }

    public function destroy(DeductionType $deductionType)
    {
        if ($deductionType->deductions()->count() > 0) {
            return back()->with('error', 'Cannot delete deduction type because it has associated deductions.');
        }

        $deductionType->delete();

        return back()->with('success', 'Deduction type deleted successfully.');
    }
}
