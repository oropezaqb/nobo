<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
            'billed_at' => ['nullable', 'date'],
            'petty' => ['required', Rule::in(['0', '1'])],
            'classification' => ['required', Rule::in(['OPEX', 'CAPEX', 'Power'])],
            'due_at' => ['required', 'date'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date'],
            'particulars' => ['required'],
            'remarks' => ['nullable'],
            'amount' => ['numeric'],
            'endorsed_at' => ['nullable', 'date'],
            'user_id' => [
                'exists:App\Models\User,id'
            ],
        ];
    }
}
