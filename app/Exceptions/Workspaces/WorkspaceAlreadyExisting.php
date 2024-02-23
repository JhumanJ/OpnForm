<?php

namespace App\Exceptions\Workspaces;

use App\Models\Workspace;
use Exception;

class WorkspaceAlreadyExisting extends Exception
{
    public function __construct(public Workspace $workspace)
    {
    }

    public function getErrorMessage()
    {
        $owner = $this->workspace->users()->first();
        if (! $owner) {
            return 'A user already connected that workspace to another NotionForms account. You or the current workspace
             owner must have a NotionForms Enterprise subscription for you to add this Notion workspace. Please upgrade
             with an Enterprise subscription, or contact us to get help.';
        }

        return '"'.$owner->name.'" already connected that workspace to his NotionForms account. In order to collaborate,
         one of you must have a NotionForms Enterprise subscription. Please upgrade or contact us to get help.';
    }
}
