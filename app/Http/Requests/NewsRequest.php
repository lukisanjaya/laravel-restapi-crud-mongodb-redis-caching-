<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class NewsRequest extends FormRequest
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
            'title'         => 'required|min:3',
            'teaser'        => 'required|min:50',
            'content'       => 'required|min:200',
            'image'         => 'required|mimes:png,jpg,jpeg|max:2048',
            'image_caption' => 'required',
            'published_at'  => 'required',
            'status'        => 'required|in:PUBLISHED,DRAFT,DELETED,CART',
            'category_id'   => 'required',
            'tag'           => 'required|array'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([

            'success'   => false,

            'message'   => 'Validation errors',

            'data'      => $validator->errors()

        ], Response::HTTP_BAD_REQUEST));
    }
}
