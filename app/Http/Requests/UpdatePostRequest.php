<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'title'=>'min:5|max:250|unique:posts,title,'.$this->route('post')->id,
            'description'=>'min:5|max:500',
            'feature_image'=>'mimes:jpg,png,jpeg',
//            'multiple_photos'=>'nullable',
//            'multiple_photos.*'=>'mimes:jpg,png,jpeg|file',
            'category'=>'exists:categories,id',
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
