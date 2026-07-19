<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête utilisée par une entreprise (ou l'admin) pour accepter
 * ou refuser une candidature reçue.
 */
class StatutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'statut' => 'required|in:acceptee,refusee',
        ];
    }
}
