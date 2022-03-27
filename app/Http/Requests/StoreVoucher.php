<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoucher extends FormRequest
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
            'number' => ['required', 'numeric', 'min:1',
            ],
            'bill_id' => [
                'exists:App\Models\Bill,id',
                'unique:App\Models\Voucher,bill_id',
            ],
            'date' => ['required', 'date'],
            'posted_at' => ['required', 'date'],
            'payable_amount' => ['required', 'numeric'],
            'remarks' => ['nullable'],
            'endorsed_at' => ['nullable', 'date'],
            'user_id' => [
                'exists:App\Models\User,id'
            ],
        ];
    }
}
