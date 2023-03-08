<?php

namespace App\TransferModule\Requests;

use App\Enums\Currency;
use App\Enums\RepeatUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_from_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')->where('user_id', $this->user()->id),
            ],
            'account_to_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')->where('user_id', $this->user()->id),
            ],
            'description' => 'required|string|max:255',
            'amount' => 'required|integer',
            'currency' => ['required', new Enum(Currency::class)],
            'date' => 'required|date',
            'hidden' => 'required|boolean',
            'auto_apply' => 'required|boolean',
            'repeat_unit' => ['required', new Enum(RepeatUnit::class)],
            'repeat_interval' => 'required|integer',
            'repeat_ends_on' => 'nullable|date',
        ];
    }
}
