<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LocaliteStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Autoriser l'accès depuis l'application mobile
        // Vous pouvez ajouter une logique d'authentification ici si nécessaire
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:20|unique:localites,code',
            'intitule' => 'required|string|max:255',
            'intitule_fr' => 'required|string|max:255',
            'nb_pop_ansade' => 'nullable|integer|min:0',
            'code_ansade' => 'nullable|string|max:255',
            'lat_loc' => 'nullable|string|max:255|regex:/^[-]?[0-9]+(\.[0-9]+)?$/',
            'long_loc' => 'nullable|string|max:255|regex:/^[-]?[0-9]+(\.[0-9]+)?$/',
            'wilaya_id' => 'required|integer|exists:wilayas,id',
            'moughata_id' => 'required|integer|exists:moughatas,id',
            'commune_id' => 'required|integer|exists:communes,id',
            'valider' => 'nullable|boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => __('localite.api.validation.code_required'),
            'code.unique' => __('localite.api.validation.code_unique'),
            'intitule.required' => __('localite.api.validation.intitule_required'),
            'intitule_fr.required' => __('localite.api.validation.intitule_fr_required'),
            'nb_pop_ansade.integer' => __('localite.api.validation.nb_pop_integer'),
            'lat_loc.regex' => __('localite.api.validation.lat_format'),
            'long_loc.regex' => __('localite.api.validation.long_format'),
            'wilaya_id.required' => __('localite.api.validation.wilaya_required'),
            'wilaya_id.exists' => __('localite.api.validation.wilaya_exists'),
            'moughata_id.required' => __('localite.api.validation.moughata_required'),
            'moughata_id.exists' => __('localite.api.validation.moughata_exists'),
            'commune_id.required' => __('localite.api.validation.commune_required'),
            'commune_id.exists' => __('localite.api.validation.commune_exists'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => __('localite.api.validation_failed'),
                'errors' => $validator->errors()
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Nettoyer les données
        $this->merge([
            'code' => trim($this->code),
            'intitule' => trim($this->intitule),
            'intitule_fr' => trim($this->intitule_fr),
            'code_ansade' => $this->code_ansade ? trim($this->code_ansade) : null,
            'lat_loc' => $this->lat_loc ? trim($this->lat_loc) : null,
            'long_loc' => $this->long_loc ? trim($this->long_loc) : null,
        ]);
    }
}