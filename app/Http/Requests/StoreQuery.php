<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuery extends FormRequest
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

    public function messages()
    {
        return [
            'title.required' => 'Title is required.',
            'category.required' => 'Category is required.',
            'query.required' => 'Query is required.',
            'permission_id.exists' => 'Invalid permission.',
            'user_id.exists' => 'Invalid user.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required'],
            'category' => ['required'],
            'query' => ['required'],
            'user_id' => [
                'exists:App\Models\User,id'
            ],
            'permission_id' => [
                'exists:App\Models\Permission,id'
            ],
        ];
    }
}
