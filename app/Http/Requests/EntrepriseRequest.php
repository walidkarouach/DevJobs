<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntrepriseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'secteur' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|string'
        ];
    }
}