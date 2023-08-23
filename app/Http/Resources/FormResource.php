<?php

namespace App\Http\Resources;

use App\Http\Middleware\Form\PasswordProtectedForm;
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
        if(!$this->userIsFormOwner() && $this->doesMissPassword($request)){
            return $this->getPasswordProtectedForm();
        }

        $ownerData = $this->userIsFormOwner() ? [
            'creator' => new UserResource($this->creator),
            'views_count' => $this->views_count,
            'submissions_count' => $this->submissions_count,
            'notifies' => $this->notifies,
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
            'seo_meta' => $this->seo_meta
        ] : [];

        $baseData = $this->getFilteredFormData(parent::toArray($request), $this->userIsFormOwner());

        return array_merge($baseData, $ownerData, [
            'is_pro' => $this->workspaceIsPro(),
            'workspace_id' => $this->workspace_id,
            'workspace' => new WorkspaceResource($this->getWorkspace()),
            'is_closed' => $this->is_closed,
            'is_password_protected' => false,
            'has_password' => $this->has_password,
            'max_number_of_submissions_reached' => $this->max_number_of_submissions_reached,
            'form_pending_submission_key' => $this->form_pending_submission_key
        ]);
    }

    /**
     * Filter form data to hide properties from users.
     * - For relation fields, hides the relation information
     */
    private function getFilteredFormData(array $data, bool $userIsFormOwner)
    {
        if ($userIsFormOwner) return $data;

        $properties = collect($data['properties'])->map(function($property){
            // Remove database details from relation
            if ($property['type'] === 'relation') {
                if (isset($property['relation'])) {
                    unset($property['relation']);
                }
            }
            return $property;
        });

        $data['properties'] = $properties->toArray();
        return $data;
    }

    public function setCleanings(array $cleanings)
    {
        $this->cleanings = $cleanings;
        return $this;
    }

    private function doesMissPassword(Request $request)
    {
        if (!$this->has_password) return false;

        return !PasswordProtectedForm::hasCorrectPassword($request, $this->resource);
    }

    private function getPasswordProtectedForm()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'custom_code' => $this->custom_code,
            'dark_mode' => $this->dark_mode,
            'transparent_background' => $this->transparent_background,
            'color' => $this->color,
            'is_password_protected' => true,
            'has_password' => $this->has_password,
            'width' => 'centered',
            'properties' => []
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
                Auth::check()
                && Auth::user()->workspaces()->find($this->workspace_id) !== null
            );
    }

    private function getCleanigns()
    {
        return $this->extra?->cleanings ?? $this->cleanings;
    }
}
