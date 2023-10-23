<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'title' => 'required|min:3|max:225|unique:posts,title',
            'description' => 'required|min:3|max:225|unique:posts,description',
            'feature_image' => 'nullable|file|mimes:jpg,png,jpeg',
            'category_id'=> 'required|exists:categories,id',
//            'multiple_photos'=>'nullable',
//            'multiple_photos.*'=>'mimes:jpg,png,jpeg|file',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Enter title ...',
            'title.min' => 'Title need at least :min length ...',
            'description.required' => 'Enter description ...',
        ];
    }
}
