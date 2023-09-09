<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
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
     * Override failed validation response
     *
     * @param Validation $validator
     *
     * @return json
     */
    protected function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(
                    [   
                        'status' => false,
                        'message' => "Pastikan Semua Field Telah Diisi Dengan Benar.",
                        'data' => $validator->errors()
                    ],
                    400
                )
            );
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "id_category" => "required|int",
            "name_product" => "required|max:50",
            "url_logo" => "required",
            "price" => "required"
        ];
    }
}
