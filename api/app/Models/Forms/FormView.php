<?php

namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormView extends Model
{
    use HasFactory;

    /**
     * RelationShips
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
