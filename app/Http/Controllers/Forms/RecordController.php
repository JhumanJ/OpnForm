<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function delete(Request $request, $id, $recordId)
    {
        $form = Form::findOrFail((int) $id);
        $this->authorize('delete', $form);

        $record = $form->submissions()->where('id', $recordId)->firstOrFail();
        $record->delete();

        return $this->success([
            'message' => 'Record successfully removed.',
        ]);
    }
}
