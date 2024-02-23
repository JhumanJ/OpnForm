<?php

namespace App\Models\Integration;

use App\Models\Forms\Form;
use App\Service\Forms\Webhooks\WebhookHandlerProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormZapierWebhook extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'form_zapier_webhooks';

    protected $fillable = [
        'form_id',
        'hook_url',
    ];

    /**
     * Relationships
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function triggerHook(array $data)
    {
        WebhookHandlerProvider::getProvider(
            $this->form,
            $data,
            WebhookHandlerProvider::ZAPIER_PROVIDER,
            $this->hook_url
        )->handle();
    }
}
