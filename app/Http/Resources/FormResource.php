<?php

namespace App\Http\Resources;

use App\Http\Middleware\Form\ProtectedForm;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FormResource extends JsonResource
{
    private array $cleanings = [];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(!$this->userIsFormOwner() && ProtectedForm::isProtected($request, $this->resource)){
            return $this->getProtectedForm();
        }

        $ownerData = $this->userIsFormOwner() ? [
            'views_count' => $this->views_count,
            'submissions_count' => $this->submissions_count,
            'notifies' => $this->notifies,
            'notifies_webhook' => $this->notifies_webhook,
            'notifies_slack' => $this->notifies_slack,
            'notifies_discord' => $this->notifies_discord,
            'send_submission_confirmation' => $this->send_submission_confirmation,
            'webhook_url' => $this->webhook_url,
            'redirect_url' => $this->redirect_url,
            'database_fields_update' => $this->database_fields_update,
            'cleanings' => $this->getCleanigns(),
            'notification_sender' => $this->notification_sender,
            'notification_subject' => $this->notification_subject,
            'notification_body' => $this->notification_body,
            'notifications_include_submission' => $this->notifications_include_submission,
            'can_be_indexed' => $this->can_be_indexed,
            'password' => $this->password,
            'tags' => $this->tags,
            'visibility' => $this->visibility,
            'notification_emails' => $this->notification_emails,
            'slack_webhook_url' => $this->slack_webhook_url,
            'discord_webhook_url' => $this->discord_webhook_url,
            'notification_settings' => $this->notification_settings,
            'removed_properties' => $this->removed_properties,
            'last_edited_human' => $this->updated_at?->diffForHumans(),
            'seo_meta' => $this->seo_meta,
        ] : [];

        return array_merge(parent::toArray($request), $ownerData, [
            'is_pro' => $this->workspaceIsPro(),
            'workspace_id' => $this->workspace_id,
            'workspace' => new WorkspaceResource($this->getWorkspace()),
            'is_closed' => $this->is_closed,
            'is_password_protected' => false,
            'has_password' => $this->has_password,
            'max_number_of_submissions_reached' => $this->max_number_of_submissions_reached,
            'form_pending_submission_key' => $this->form_pending_submission_key,
            'max_file_size' => $this->max_file_size / 1000000,
        ]);
    }

    public function setCleanings(array $cleanings)
    {
        $this->cleanings = $cleanings;
        return $this;
    }

    private function getProtectedForm()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'slug' => $this->slug,
            'custom_code' => $this->custom_code,
            'dark_mode' => $this->dark_mode,
            'transparent_background' => $this->transparent_background,
            'color' => $this->color,
            'theme' => $this->theme,
            'is_password_protected' => true,
            'has_password' => $this->has_password,
            'width' => 'centered',
            'no_branding' => $this->no_branding,
            'properties' => [],
            'logo_picture' => $this->logo_picture,
            'seo_meta' => $this->seo_meta,
            'cover_picture' => $this->cover_picture,

        ];
    }

    private function getWorkspace() {
        return $this->extra?->loadedWorkspace ?? $this->workspace;
    }

    private function workspaceIsPro() {
        return $this->extra?->workspaceIsPro ?? $this->getWorkspace()->is_pro ?? $this->is_pro;
    }

    private function userIsFormOwner() {
        return $this->extra?->userIsOwner ??
            (
                Auth::check() && Auth::user()->ownsForm($this->resource)
            );
    }

    private function getCleanigns()
    {
        return $this->extra?->cleanings ?? $this->cleanings;
    }
}
