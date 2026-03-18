<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PerformanceCriteria;
class PerformanceCriteriaController extends Controller
{
    //

      /**
     * Display a listing of performance criteria
     */
    public function index(Request $request)
    {
        $query = PerformanceCriteria::withCount('ratings');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $criteria = $query->orderBy('category')->orderBy('name')->paginate(15);

        // Statistics
        $stats = [
            'total_criteria' => PerformanceCriteria::count(),
            'active_criteria' => PerformanceCriteria::where('is_active', true)->count(),
            'inactive_criteria' => PerformanceCriteria::where('is_active', false)->count(),
            'total_weight' => PerformanceCriteria::where('is_active', true)->sum('weight'),
        ];

        return view('admin.performance.criteria.index', compact('criteria', 'stats'));
    }

    /**
     * Show the form for creating a new criterion
     */
    public function create()
    {
        return view('admin.performance.criteria.create');
    }

    /**
     * Store a newly created criterion
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:technical,behavioral,leadership,communication,productivity',
            'weight' => 'required|integer|min:1|max:100',
            'is_active' => 'required|boolean',
        ]);

        try {
            PerformanceCriteria::create([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'weight' => $request->weight,
                'is_active' => $request->is_active,
            ]);

            return redirect()->route('admin.performance.criteria.index')
                ->with('success', 'Performance criterion created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create criterion: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified criterion
     */
    public function show($id)
    {
        $criterion = PerformanceCriteria::withCount('ratings')->findOrFail($id);

        return view('admin.performance.criteria.show', compact('criterion'));
    }

    /**
     * Show the form for editing the specified criterion
     */
    public function edit($id)
    {
        $criterion = PerformanceCriteria::findOrFail($id);

        return view('admin.performance.criteria.edit', compact('criterion'));
    }

    /**
     * Update the specified criterion
     */
    public function update(Request $request, $id)
    {
        $criterion = PerformanceCriteria::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:technical,behavioral,leadership,communication,productivity',
            'weight' => 'required|integer|min:1|max:100',
            'is_active' => 'required|boolean',
        ]);

        try {
            $criterion->update([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'weight' => $request->weight,
                'is_active' => $request->is_active,
            ]);

            return redirect()->route('admin.performance.criteria.index')
                ->with('success', 'Performance criterion updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Failed to update criterion: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle criterion active status
     */
    public function toggleStatus($id)
    {
        try {
            $criterion = PerformanceCriteria::findOrFail($id);

            $criterion->is_active = !$criterion->is_active;
            $criterion->save();

            return response()->json([
                'success' => true,
                'message' => 'Criterion status updated successfully.',
                'is_active' => $criterion->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified criterion
     */
    public function destroy($id)
    {
        try {
            $criterion = PerformanceCriteria::findOrFail($id);

            // Check if criterion has ratings
            if ($criterion->ratings()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete criterion that has been used in reviews.'
                ], 422);
            }

            $criterion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Criterion deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete criterion: ' . $e->getMessage()
            ], 500);
        }
    }
}
