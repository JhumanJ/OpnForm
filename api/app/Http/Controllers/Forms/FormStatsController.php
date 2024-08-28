<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use Carbon\CarbonPeriod;
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

        $formStats = $form->statistics()->where('date', '>', now()->subDays(29)->startOfDay())->get();
        $periodStats = ['views' => [], 'submissions' => []];
        foreach (CarbonPeriod::create(now()->subDays(29), now()) as $dateObj) {
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
}
