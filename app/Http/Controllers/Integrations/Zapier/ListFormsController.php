<?php

namespace App\Http\Controllers\Integrations\Zapier;

use App\Http\Requests\Zapier\ListFormsRequest;
use App\Http\Resources\Zapier\FormResource;

class ListFormsController
{
    public function __invoke(ListFormsRequest $request)
    {
        return FormResource::collection(
            $request->workspace()->forms()->get()
        );
    }
}
