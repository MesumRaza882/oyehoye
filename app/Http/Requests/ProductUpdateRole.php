<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRole extends FormRequest
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
            'name' => 'required|string',
            'price' => 'required|integer',
            'purchase' => 'required|integer',
            'profit' => 'required|integer',
            'article' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'soldItem' => 'required|integer',
            'increase_perMin' => 'required|integer',
            'video' => 'mimes:mp4',
            'stop_fake_after_quantity' => 'required|integer',
        ];
    }
}
