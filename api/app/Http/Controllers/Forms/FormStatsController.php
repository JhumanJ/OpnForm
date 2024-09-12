<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;

class FormStatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getFormStats(Request $request)
    {
        $form = $request->form; // Added by ProForm middleware
        $this->authorize('view', $form);

        if (!$request->date_from || !$request->date_to) {
            return $this->error([
                'message' => 'Date range is required. Please select a date range.',
            ]);
        }

        // Check if the date range is more than 3 months
        if (Carbon::parse($request->date_from)->diffInMonths(Carbon::parse($request->date_to)) > 3) {
            return $this->error([
                'message' => 'Date range exceeds 3 months. Please select a shorter period.',
            ]);
        }

        $formStats = $form->statistics()->where('date', '>', now()->subDays(29)->startOfDay())->get();
        $periodStats = ['views' => [], 'submissions' => []];
        foreach (CarbonPeriod::create($request->date_from, $request->date_to) as $dateObj) {
            $date = $dateObj->format('d-m-Y');

            $statisticData = $formStats->where('date', $dateObj->format('Y-m-d'))->first();
            $periodStats['views'][$date] = $statisticData->data['views'] ?? 0;
            $periodStats['submissions'][$date] = $form->submissions()->whereDate('created_at', $dateObj)->count();

            if ($dateObj->toDateString() === now()->toDateString()) {
                $periodStats['views'][$date] += $form->views()->count();
            }
        }

        return $periodStats;
    }

    public function getFormStatsDetails(Request $request)
    {
        $form = $request->form; // Added by ProForm middleware
        $this->authorize('view', $form);

        $totalViews = $form->views()->count();
        $totalSubmissions = $form->submissions_count;
        $submissionsWithDuration = $form->submissions()->whereNotNull('completion_time')->count() ?? 0;
        $totalDuration = $form->submissions()->whereNotNull('completion_time')->sum('completion_time') ?? 0;
        $averageDuration = $submissionsWithDuration > 0 ? round($totalDuration / $submissionsWithDuration) : 0;

        return [
            'views' => $totalViews,
            'submissions' => $totalSubmissions,
            'completion_rate' => $totalViews > 0 ? round(($totalSubmissions / $totalViews) * 100, 2) : 0,
            'average_duration' => CarbonInterval::seconds($averageDuration)->cascade()->forHumans()
        ];
    }
}
