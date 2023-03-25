<?php

namespace App\PaymentModule\Requests;

use App\Enums\Currency;
use App\Enums\RepeatUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StorePaymentRequest extends FormRequest
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
                'required_without:account_from_id',
                'integer',
                Rule::exists('accounts', 'id')->where('user_id', $this->user()->id),
            ],
            'account_from_id' => [
                'required_without:account_to_id',
                'integer',
                Rule::exists('accounts', 'id')->where('user_id', $this->user()->id),
            ],
            'description' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
            'currency' => ['required', new Enum(Currency::class)],
            'date' => 'required|date',
            'auto_apply' => 'required|boolean',
            'budget' => 'required|boolean',
            'repeat_unit' => ['required', new Enum(RepeatUnit::class)],
            'repeat_interval' => 'required|integer|min:1',
            'repeat_ends_on' => 'nullable|date|after:date',
        ];
    }
}
