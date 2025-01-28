<?php

namespace App\Http\Requests\WaoSeller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class SellerOrderRequestMnp extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'selected_articles' => 'required',
            'mnp_username' =>  'required',
            'mnp_password' =>  'required',
            'locationID' =>  'required',
            'fragile' =>  'required',
            'service' =>  'required',
            'estimated_weight' =>  'required',
            // customer
            'consignee_phone_number_1' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            'consignee_whatsaapp' => 'nullable|regex:/^((0))(3)([0-9]{9})$/',
            'consignee_name' => 'required',
            'consignee_city' => 'required:exists:cities,name',
            'consignee_address' => 'required',
            // order
            'grandTotal' => 'required',
            'total' => 'required',
            'charges' => 'required',
            'item_quantity' => 'required',
            'item_description' => 'required',
            'order_id' => ['required', 'regex:/^(?!00)/'],
        ];
    }

    public function failedValidation(Validator $validator)

    {
        throw new HttpResponseException(response()->json([
            'status'   => 0,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()->first(),
        ]));
    }
}
