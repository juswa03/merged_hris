<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerformanceReview;
use App\Models\PerformanceGoal;
use App\Models\Department;
use Illuminate\Http\Request;

class PerformanceAnalyticsController extends Controller
{
    public function analytics()
    {
        $totalReviews = PerformanceReview::count();
        $completedReviews = PerformanceReview::where('status', 'completed')->count();
        $pendingReviews = PerformanceReview::whereIn('status', ['pending', 'in_progress'])->count();
        $activeGoals = PerformanceGoal::whereIn('status', ['not_started', 'in_progress'])->count();

        $completionRate = $totalReviews > 0
            ? round(($completedReviews / $totalReviews) * 100, 2)
            : 0;

        // Rating distribution
        $reviews = PerformanceReview::whereNotNull('overall_rating')->get();
        $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($reviews as $review) {
            $bucket = (int) ceil($review->overall_rating);
            if ($bucket >= 1 && $bucket <= 5) {
                $ratingCounts[$bucket]++;
            }
        }

        $ratingDistribution = [];
        foreach ($ratingCounts as $rating => $count) {
            $ratingDistribution[] = [
                'overall_rating' => $rating,
                'count'          => $count,
                'percentage'     => $totalReviews > 0 ? round(($count / $totalReviews) * 100, 1) : 0,
            ];
        }

        // Department performance
        $departmentPerformance = Department::select('tbl_departments.id', 'tbl_departments.name')
            ->leftJoin('tbl_employee', 'tbl_employee.department_id', '=', 'tbl_departments.id')
            ->leftJoin('tbl_performance_reviews', 'tbl_performance_reviews.employee_id', '=', 'tbl_employee.id')
            ->groupBy('tbl_departments.id', 'tbl_departments.name')
            ->selectRaw('
                tbl_departments.name as name,
                COUNT(tbl_performance_reviews.id) as total_reviews,
                SUM(CASE WHEN tbl_performance_reviews.status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN tbl_performance_reviews.status IN ("pending", "in_progress") THEN 1 ELSE 0 END) as pending,
                ROUND(AVG(tbl_performance_reviews.overall_rating), 2) as avg_rating
            ')
            ->get()
            ->map(fn($d) => [
                'name'          => $d->name,
                'total_reviews' => (int) $d->total_reviews,
                'completed'     => (int) $d->completed,
                'pending'       => (int) $d->pending,
                'avg_rating'    => $d->avg_rating ?? 0,
            ])
            ->toArray();

        // Recent reviews
        $recentReviews = PerformanceReview::with('employee')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.performance.analytics', compact(
            'totalReviews',
            'completedReviews',
            'pendingReviews',
            'activeGoals',
            'completionRate',
            'ratingDistribution',
            'departmentPerformance',
            'recentReviews'
        ));
    }
}

