<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GameRequest extends FormRequest
{
    public function authorize()
    {
        // Assuming authorization logic goes here (return true or false)
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'date_time' => 'required|date',
            'mode_id' => 'required|exists:modes,id',
            'location' => 'required|string|max:255',
        ];
    }
}

