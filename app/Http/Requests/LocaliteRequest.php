<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocaliteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'code' => 'required|string|max:20|unique:localites,code',
            'intitule' => 'required|string|max:255',
            'intitule_fr' => 'required|string|max:255',
            'nb_pop_ansade' => 'nullable|integer|min:0',
            'code_ansade' => 'nullable|string|max:255',
            'lat_loc' => 'nullable|string|max:255',
            'long_loc' => 'nullable|string|max:255',
            'wilaya_id' => 'required|exists:wilayas,id',
            'moughata_id' => 'required|exists:moughatas,id',
            'commune_id' => 'required|exists:communes,id',
            'valider' => 'boolean',
        ];

        // Pour la mise à jour, on ignore l'unicité du code pour l'enregistrement courant
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['code'] = 'required|string|max:20|unique:localites,code,' . $this->route('localite');
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => __('Le code est obligatoire'),
            'code.unique' => __('Ce code existe déjà'),
            'intitule.required' => __('L\'intitulé est obligatoire'),
            'intitule_fr.required' => __('L\'intitulé en français est obligatoire'),
            'wilaya_id.required' => __('La wilaya est obligatoire'),
            'moughata_id.required' => __('La moughata est obligatoire'),
            'commune_id.required' => __('La commune est obligatoire'),
        ];
    }
}