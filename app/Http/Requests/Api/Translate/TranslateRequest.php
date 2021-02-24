<?php

namespace App\Http\Requests\Api\Translate;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class TranslateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rule = [];

        $language = Language::where('id', $this->input('language_id', 0))->count();
        if (!$language) {
            $rule['language_id'] = ['max:0'];
        }

        return $rule;
    }

    public function messages()
    {
        return [
            'language_id.max' => 'Нет такого языка',
        ];
    }
}
