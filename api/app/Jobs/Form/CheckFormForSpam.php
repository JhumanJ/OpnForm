<?php

namespace App\Jobs\Form;

use App\Models\Forms\Form;
use App\Service\Forms\FormSpamService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckFormForSpam implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Form $form)
    {
    }

    public function handle(FormSpamService $formSpamService): void
    {
        $formSpamService->checkForm($this->form);
    }
}
