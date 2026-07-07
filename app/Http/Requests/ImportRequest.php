<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'operation_id' => 'required',
            'wilaya_id' => 'required',
            'file' => 'required|file|mimes:xls,xlsx|max:204800'
        ];

    }
         /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'operation_id.required' => 'Email is required!',
            'wilaya_id.required' => 'Name is required!',
            'file.required' => 'Password is required!',
            'file.file' => 'Password is required!',
            'file.mimes' => 'Excel File Only!',
            'file.max' => 'Password is required!',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        
        
    
        throw new HttpResponseException(redirect()->back()->with(['errors' => $validator->errors()]));
        
        //
    }
}
