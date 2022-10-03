<?php

namespace App\PaymentModule\Requests;

use App\Enums\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdatePaymentRequest extends FormRequest
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
            'jar_id'      => 'required|integer|exists:jars,id',
            'description' => 'required|string|max:255',
            'amount'      => 'required|integer',
            'currency'    => ['required', new Enum(Currency::class)],
            'date'        => 'required|date',
            'hidden'      => 'required|boolean',
            'ends_on'     => 'nullable|date',
        ];
    }
}
