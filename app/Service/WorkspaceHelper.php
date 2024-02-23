<?php

namespace App\Service;

use App\Models\Workspace;

class WorkspaceHelper
{
    public function __construct(public Workspace $workspace)
    {

    }

    public function getRecords($relatedRecordIds = null)
    {
        return [];
    }
}
