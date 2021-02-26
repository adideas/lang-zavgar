<?php

namespace App\Http\Requests\Api\File;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FileRequestStore extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required'],
            'is_file' => ['required'],
            'parent' => ['required'],
            'file_type' => [Rule::requiredIf(function () {
                return $this->input('is_file', 0) == 1;
            })],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Имя обязательно',
            'parent.required' => 'Имя обязательно',
            'is_file.required' => 'Тип обязательно',
            'file_type.required' => 'Тип обязательно',
        ];
    }
}
