<?php

namespace App\Events\Forms;

use App\Models\Forms\Form;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FormSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $form;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Form $form, array $data)
    {
        $this->form = $form;
        $this->data = $data;
    }
}
