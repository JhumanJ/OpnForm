<?php

namespace App\Models\Forms;

use App\Models\Forms\Form;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormView extends Model
{
    use HasFactory;

    /**
     * RelationShips
     */
    public function form() {
        return $this->belongsTo(Form::class);
    }
}
