<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class ProductStoreRule extends FormRequest
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
            'name' => 'required|unique:products,name,NULL,id,deleted_at,NULL',
            'article' => 'required|unique:products,article,NULL,id,deleted_at,NULL',
            // 'name' => [
            //     'required',
            //     Rule::unique('products', 'article')->whereNull('deleted_at'),
            // ],
            'price' => 'required|integer',
            'purchase' => 'required|integer',
            'profit' => 'required|integer',
            // 'article' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'soldItem' => 'required|integer',
            'increase_perMin' => 'required|integer',
            'video' => 'required|mimes:mp4',
            'thumbnail' => 'required|image',
            'stop_fake_after_quantity' => 'required|integer',
        ];
    }
}
