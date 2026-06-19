<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyzeCvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'candidate_name' => ['required', 'string', 'max:255'],
            'cv_text' => ['required', 'string', 'min:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'cv_text.min' => 'Le texte du CV doit contenir au moins 50 caractères.',
            'candidate_name.required' => 'Le nom du candidat est requis.',
        ];
    }
}
