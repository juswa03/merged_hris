<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Holiday;
class HolidayController extends Controller
{
    //

       /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'desc')->paginate(10);
        return view('admin.holidays.index', compact('holidays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.holidays.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'date'     => 'required|date|unique:tbl_holidays,date',
            'type'     => 'required|in:regular,special',
            'is_paid'  => 'boolean',
            'remarks'  => 'nullable|string',
        ]);

        $validated['is_paid'] = $request->boolean('is_paid');

        Holiday::create($validated);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Holiday $holiday)
    {
        return view('admin.holidays.show', compact('holiday'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Holiday $holiday)
    {
        return view('admin.holidays.edit', compact('holiday'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'date'     => 'required|date|unique:tbl_holidays,date,' . $holiday->id,
            'type'     => 'required|in:regular,special',
            'is_paid'  => 'boolean',
            'remarks'  => 'nullable|string',
        ]);

        $validated['is_paid'] = $request->boolean('is_paid');

        $holiday->update($validated);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday deleted successfully.');
    }
}
