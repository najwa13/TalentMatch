<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'required_skills' => ['required', 'array', 'min:1'],
            'required_skills.*' => ['string', 'filled'],
            'minimum_experience' => ['required', 'integer', 'min:0', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est requis.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'description.required' => 'La description est requise.',
            'required_skills.required' => 'Les compétences sont requises.',
            'required_skills.min' => 'Au moins une compétence est requise.',
            'minimum_experience.required' => 'L\'expérience minimale est requise.',
            'minimum_experience.integer' => 'L\'expérience minimale doit être un nombre entier.',
            'minimum_experience.min' => 'L\'expérience minimale ne peut pas être négative.',
            'minimum_experience.max' => 'L\'expérience minimale ne peut pas dépasser 50 ans.',
        ];
    }
}
