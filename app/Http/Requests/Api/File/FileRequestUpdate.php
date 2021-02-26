<?php

namespace App\Http\Requests\Api\File;

use Illuminate\Foundation\Http\FormRequest;

class FileRequestUpdate extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->input('path_parents')) {
            return [];
        }
        return [
            'name' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Имя обязательно'
        ];
    }
}
