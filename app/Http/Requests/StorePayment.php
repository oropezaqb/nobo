<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayment extends FormRequest
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
            'voucher_id' => [
                'exists:App\Models\Voucher,id',
            ],
            'remarks' => ['nullable'],
            'check_number' => ['nullable'],
            'check_date' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'cleared_at' => ['nullable', 'date'],
            'user_id' => [
                'exists:App\Models\User,id'
            ],
        ];
    }
}
