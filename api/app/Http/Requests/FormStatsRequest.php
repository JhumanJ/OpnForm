<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class FormStatsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (Carbon::parse($this->date_from)->diffInMonths(Carbon::parse($this->date_to)) > 3) {
                $validator->errors()->add('date_range', 'Date range exceeds 3 months. Please select a shorter period.');
            }
        });
    }
}
