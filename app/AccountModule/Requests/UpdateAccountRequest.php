<?php

namespace App\AccountModule\Requests;

use App\Enums\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateAccountRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'currency' => ['required', new Enum(Currency::class)],
            'balance' => 'required|integer',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::notIn([$this->route('account')->id]),
                Rule::exists('accounts', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }
}
