<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBill extends FormRequest
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
            'received_at' => ['required', 'date'],
            'payee_id' => [
                'exists:App\Models\Payee,id'
            ],
            'bill_number' => ['nullable'],
            'po_number' => ['nullable'],
            'due_at' => ['required', 'date'],
            'period_start' => ['date'],
            'period_end' => ['date'],
            'particulars' => ['required'],
            'remarks' => ['nullable'],
            'amount' => ['numeric', 'min:0.01', 'required'],
            'endorsed_at' => ['nullable', 'date'],
            'user_id' => [
                'exists:App\Models\User,id'
            ],
        ];
    }
}
