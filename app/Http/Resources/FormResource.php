<?php

namespace App\Http\Resources;

use App\Http\Middleware\Form\PasswordProtectedForm;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FormResource extends JsonResource
{
    private Array $cleanings = [];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $userIsFormOwner = Auth::check() && Auth::user()->workspaces()->find($this->workspace_id) !== null;
        if(!$userIsFormOwner && $this->doesMissPassword($request)){
            return $this->getPasswordProtectedForm();
        }

        $ownerData = $userIsFormOwner ? [
            'creator' => $this->creator,
            'views_count' => $this->when($this->workspace->is_pro, $this->views_count),
            'submissions_count' => $this->when($this->workspace->is_pro, $this->submissions_count),
            'notifies' => $this->notifies,
            'notifies_slack' => $this->notifies_slack,
            'send_submission_confirmation' => $this->send_submission_confirmation,
            'webhook_url' => $this->webhook_url,
            'redirect_url' => $this->redirect_url,
            'database_fields_update' => $this->database_fields_update,
            'cleanings' => $this->cleanings,
            'notification_sender' => $this->notification_sender,
            'notification_subject' => $this->notification_subject,
            'notification_body' => $this->notification_body,
            'notifications_include_submission' => $this->notifications_include_submission,
            'can_be_indexed' => $this->can_be_indexed,
            'password' => $this->password,
            'tags' => $this->tags,
            'notification_emails' => $this->notification_emails,
            'slack_webhook_url' => $this->slack_webhook_url,
        ] : [];

        $baseData = $this->getFilteredFormData(parent::toArray($request), $userIsFormOwner);

        return array_merge($baseData, $ownerData, [
            'workspace_id' => $this->workspace_id,
            'workspace' => new WorkspaceResource($this->workspace),
            'is_closed' => $this->is_closed,
            'is_password_protected' => false,
            'has_password' => $this->has_password,
            'max_number_of_submissions_reached' => $this->max_number_of_submissions_reached
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
        if (!$this->is_pro || !$this->has_password) return false;

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
}
