<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderRequest extends FormRequest
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
            "name_order" => "required|max:50",
            "amount" => "required",
            "tax" => "required",
            "service" => "required",
            "total_amount" => "required",
            "payment_method" => "required",
            "array_product" => "required",
            "type_order" => "required",
        ];
    }
}
