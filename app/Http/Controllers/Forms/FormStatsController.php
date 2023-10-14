<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use Carbon\CarbonPeriod;

class FormStatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getFormStats(string $formId)
    {
        $form = Form::findOrFail($formId);

        $this->authorize('view', $form);

        $formStats = $form->statistics()->where('date','>',now()->subDays(29)->startOfDay())->get();
        $periodStats = ["views" => [], "submissions" => []];
        foreach (CarbonPeriod::create(now()->subDays(29), now()) as $dateObj) {
            $date = $dateObj->format('d-m-Y');

            $statisticData = $formStats->where('date', $dateObj->format('Y-m-d'))->first();
            $periodStats["views"][$date] = $statisticData->data["views"] ?? 0;
            $periodStats["submissions"][$date] = $statisticData->data["submissions"] ?? 0;

            if($dateObj->toDateString() === now()->toDateString()){
                $periodStats["views"][$date] += $form->views()->count();
                $periodStats["submissions"][$date] += $form->submissions()->whereDate('created_at', '>=', now()->startOfDay())->count();
            }
        }
        return $periodStats;
    }
}
