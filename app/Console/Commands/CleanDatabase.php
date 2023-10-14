<?php

namespace App\Console\Commands;

use App\Models\Forms\FormStatistic;
use Illuminate\Console\Command;
use App\Models\Forms\FormView;
use App\Models\Forms\FormSubmission;
use Illuminate\Support\Facades\DB;

class CleanDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:database-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Database Cleanup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->cleanFormStatistics();

        $this->line('Database Cleanup Success.');
    }

    /**
     * Manage FormViews & FormSubmissions records
     */
    private function cleanFormStatistics()
    {
        $this->line('Aggregating form views...');
        $now = now();
        $finalData = [];

        // Form Views
        FormView::select('form_id', DB::raw('DATE(created_at) as date'), DB::raw('count(*) as views'))
            ->where('created_at', '<', $now)
            ->orderBy('date')
            ->groupBy('form_id', 'date')
            ->get()->each(function ($row) use (&$finalData) {
                $finalData[$row->form_id."-".$row->date] = [
                    'form_id' => $row->form_id,
                    'date' => $row->date,
                    'data' => [
                        'views' => $row->views,
                        'submissions' => 0
                    ]
                ];
            });

        // Form Submissions
        $this->line('Aggregating form submissions...');
        FormSubmission::select('form_id', DB::raw('DATE(created_at) as date'), DB::raw('count(*) as submissions'))
            ->whereDate('created_at', '<=', now()->startOfDay())
            ->orderBy('date')
            ->groupBy('form_id', 'date')
            ->get()->each(function ($row) use (&$finalData) {
                $key = $row->form_id."-".$row->date;
                if (isset($finalData[$key])) {
                    $finalData[$key]['data']['submissions'] = $row->submissions;
                } else {
                    $finalData[$key] = [
                        'form_id' => $row->form_id,
                        'date' => $row->date,
                        'data' => [
                            'views' => 0,
                            'submissions' => $row->submissions
                        ]
                    ];
                }

            });

        if ($finalData) {
            $this->line('Storing aggregated data...');
            $created = 0;
            $updated = 0;
            // Insert into Form Statistic
            foreach ($finalData as $row) {
                $found = FormStatistic::where([['form_id', $row['form_id']], ['date', $row['date']]])->first();
                if ($found !== null) { // If found update
                    $newData = $found->data;
                    $newData['views'] = $newData['views'] + $row['data']['views'];
                    $newData['submissions'] = $newData['submissions'] + $row['data']['submissions'];
                    $found->update(['data' => $newData]);
                    $updated++;
                } else {  // Otherwise create new
                    FormStatistic::create($row);
                    $created++;
                }
            }

            $this->line($created.' form statistics records created.');
            $this->line($updated.' form statistics records updated.');

            // Delete Form Views those are migrated
            $formViewRemovedCount = FormView::where('created_at', '<', $now)->delete();
            $this->line($formViewRemovedCount.' form views records deleted.');
        } else {
            $this->line('No aggregate to store.');
        }
    }
}
