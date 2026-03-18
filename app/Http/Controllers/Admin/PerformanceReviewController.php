<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\PerformanceReview;
use App\Models\Employee;
use App\Models\PerformanceCriteria;
use App\Models\PerformanceRating;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Exports\PerformanceReviewExport;
use Maatwebsite\Excel\Facades\Excel;

class PerformanceReviewController extends Controller
{
    //

       /**
     * Display a listing of performance reviews
     */
    public function index(Request $request)
    {
        $query = PerformanceReview::with(['employee.department', 'reviewer']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by review type
        if ($request->has('review_type') && $request->review_type) {
            $query->where('review_type', $request->review_type);
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total_reviews' => PerformanceReview::count(),
            'pending_reviews' => PerformanceReview::where('status', 'pending')->count(),
            'completed_reviews' => PerformanceReview::where('status', 'completed')->count(),
            'avg_rating' => PerformanceReview::whereNotNull('overall_rating')->avg('overall_rating'),
        ];

        $departments = \App\Models\Department::all();

        return view('admin.performance.reviews.index', compact('reviews', 'stats', 'departments'));
    }

    /**
     * Show the form for creating a new review
     */
    public function create()
    {
        $employees = Employee::with('department')->get();
        $criteria = PerformanceCriteria::active()->get();

        return view('admin.performance.reviews.create', compact('employees', 'criteria'));
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:tbl_employee,id',
            'review_period_start' => 'required|date',
            'review_period_end' => 'required|date|after:review_period_start',
            'review_type' => 'required|in:quarterly,annual,probation,mid_year',
        ]);

        try {
            DB::beginTransaction();

            $review = PerformanceReview::create([
                'employee_id' => $request->employee_id,
                'reviewer_id' => auth()->id(),
                'review_period_start' => $request->review_period_start,
                'review_period_end' => $request->review_period_end,
                'review_type' => $request->review_type,
                'status' => 'draft',
            ]);

            DB::commit();

            return redirect()->route('admin.performance.reviews.evaluate', $review->id)
                ->with('success', 'Performance review created. Please complete the evaluation.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create review: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified review
     */
    public function show($id)
    {
        $review = PerformanceReview::with(['employee.department', 'reviewer', 'ratings.criteria', 'approver'])->findOrFail($id);

        return view('admin.performance.reviews.show', compact('review'));
    }

    /**
     * Show the evaluation form
     */
    public function evaluate($id)
    {
        $review = PerformanceReview::with(['employee', 'ratings.criteria'])->findOrFail($id);
        $criteria = PerformanceCriteria::active()->get();

        // Check if ratings already exist, if not create them
        if ($review->ratings->isEmpty()) {
            foreach ($criteria as $criterion) {
                PerformanceRating::create([
                    'performance_review_id' => $review->id,
                    'performance_criteria_id' => $criterion->id,
                    'rating' => 3, // Default rating
                ]);
            }
            $review->load('ratings.criteria');
        }

        return view('admin.performance.reviews.evaluate', compact('review', 'criteria'));
    }

    /**
     * Store the evaluation ratings
     */
    public function storeEvaluation(Request $request, $id)
    {
        $review = PerformanceReview::findOrFail($id);

        $request->validate([
            'ratings' => 'required|array',
            'ratings.*.rating' => 'required|integer|min:1|max:5',
            'ratings.*.comments' => 'nullable|string',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'reviewer_comments' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update ratings
            foreach ($request->ratings as $criteriaId => $ratingData) {
                PerformanceRating::updateOrCreate(
                    [
                        'performance_review_id' => $review->id,
                        'performance_criteria_id' => $criteriaId,
                    ],
                    [
                        'rating' => $ratingData['rating'],
                        'comments' => $ratingData['comments'] ?? null,
                    ]
                );
            }

            // Reload ratings to calculate overall rating
            $review->load('ratings.criteria');
            $overallRating = $review->calculateOverallRating();

            // Update review
            $review->update([
                'strengths' => $request->strengths,
                'areas_for_improvement' => $request->areas_for_improvement,
                'recommendations' => $request->recommendations,
                'reviewer_comments' => $request->reviewer_comments,
                'overall_rating' => $overallRating,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.performance.reviews.show', $review->id)
                ->with('success', 'Performance review completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Failed to save evaluation: ' . $e->getMessage()]);
        }
    }

    /**
     * Update review status
     */
    public function updateStatus(Request $request, $id)
    {
        $review = PerformanceReview::findOrFail($id);

        $request->validate([
            'status' => 'required|in:draft,pending,completed,approved',
            'hr_comments' => 'nullable|string',
        ]);

        try {
            $data = [
                'status' => $request->status,
            ];

            if ($request->status === 'approved') {
                $data['approved_at'] = now();
                $data['approved_by'] = auth()->id();
            }

            if ($request->hr_comments) {
                $data['hr_comments'] = $request->hr_comments;
            }

            $review->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Review status updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified review
     */
    public function destroy($id)
    {
        try {
            $review = PerformanceReview::findOrFail($id);

            // Only allow deletion of draft reviews
            if ($review->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft reviews can be deleted.'
                ], 422);
            }

            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Performance analytics and reports
     */
    public function analytics()
    {
        $stats = [
            'total_reviews' => PerformanceReview::count(),
            'avg_rating' => PerformanceReview::whereNotNull('overall_rating')->avg('overall_rating'),
            'outstanding_performers' => PerformanceReview::where('overall_rating', '>=', 4.5)->count(),
            'needs_improvement' => PerformanceReview::where('overall_rating', '<', 2.5)->count(),
        ];

        // Rating distribution
        $ratingDistribution = PerformanceReview::selectRaw('
            COUNT(CASE WHEN overall_rating >= 4.5 THEN 1 END) as outstanding,
            COUNT(CASE WHEN overall_rating >= 3.5 AND overall_rating < 4.5 THEN 1 END) as exceeds,
            COUNT(CASE WHEN overall_rating >= 2.5 AND overall_rating < 3.5 THEN 1 END) as meets,
            COUNT(CASE WHEN overall_rating >= 1.5 AND overall_rating < 2.5 THEN 1 END) as needs_improvement,
            COUNT(CASE WHEN overall_rating < 1.5 THEN 1 END) as unsatisfactory
        ')->first();

        // Department average ratings
        $departmentRatings = PerformanceReview::join('tbl_employee', 'tbl_performance_reviews.employee_id', '=', 'tbl_employee.id')
            ->join('tbl_departments', 'tbl_employee.department_id', '=', 'tbl_departments.id')
            ->selectRaw('tbl_departments.name as department, AVG(tbl_performance_reviews.overall_rating) as avg_rating, COUNT(*) as review_count')
            ->whereNotNull('tbl_performance_reviews.overall_rating')
            ->groupBy('tbl_departments.id', 'tbl_departments.name')
            ->get();

        // Recent reviews
        $recentReviews = PerformanceReview::with(['employee', 'reviewer'])
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.performance.analytics', compact('stats', 'ratingDistribution', 'departmentRatings', 'recentReviews'));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['department_id', 'reviewer_id', 'year', 'quarter']);
        $timestamp = now()->format('Y-m-d_His');
        $filename = "performance_reviews_export_{$timestamp}.xlsx";
        return Excel::download(new PerformanceReviewExport($filters), $filename);
    }
}
