<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmploymentType;
use Illuminate\Http\Request;

class EmploymentTypeController extends Controller
{
    public function index()
    {
        $types = EmploymentType::withCount('employees')->orderBy('name')->paginate(20);

        $stats = [
            'total'  => EmploymentType::count(),
            'in_use' => EmploymentType::has('employees')->count(),
            'unused' => EmploymentType::doesntHave('employees')->count(),
        ];

        return view('admin.employment-types.index', compact('types', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:tbl_employment_type,name',
            'description' => 'nullable|string|max:500',
        ]);

        EmploymentType::create($request->only('name', 'description'));

        return back()->with('success', 'Employment type "' . $request->name . '" created successfully.');
    }

    public function update(Request $request, EmploymentType $employmentType)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:tbl_employment_type,name,' . $employmentType->id,
            'description' => 'nullable|string|max:500',
        ]);

        $employmentType->update($request->only('name', 'description'));

        return back()->with('success', 'Employment type updated successfully.');
    }

    public function destroy(EmploymentType $employmentType)
    {
        $count = $employmentType->employees()->count();

        if ($count > 0) {
            return back()->with('error', "Cannot delete \"{$employmentType->name}\": it is assigned to {$count} employee(s).");
        }

        $employmentType->delete();

        return back()->with('success', 'Employment type deleted.');
    }
}
