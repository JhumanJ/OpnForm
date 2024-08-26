<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class OpenFormMail extends Mailable
{
    use Queueable;
    use SerializesModels;
}
