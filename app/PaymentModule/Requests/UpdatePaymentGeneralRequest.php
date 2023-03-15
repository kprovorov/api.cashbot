<?php

namespace App\PaymentModule\Requests;

use App\Enums\Currency;
use App\Enums\PaymentUpdateMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdatePaymentGeneralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'account_to_id' => [
                'nullable',
                'required_without:account_from_id',
                'integer',
                Rule::exists('accounts', 'id')->where('user_id', $this->user()->id),
            ],
            'account_from_id' => [
                'nullable',
                'required_without:account_to_id',
                'integer',
                Rule::exists('accounts', 'id')->where('user_id', $this->user()->id),
            ],
            'description' => 'required|string|max:255',
            'amount' => 'required|integer',
            'currency' => ['required', new Enum(Currency::class)],
            'from_date' => 'required|date',
            'mode' => ['required', new Enum(PaymentUpdateMode::class)],
        ];
    }
}
