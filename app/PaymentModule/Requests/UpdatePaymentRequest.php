<?php

namespace App\PaymentModule\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'jar_id' => 'required|integer',
            'description' => 'required|string|max:255',
            'amount' => 'required|integer',
            'currency' => 'required|string|max:255',
            'date' => 'required|date',
            'hidden' => 'required|boolean',
            'ends_on' => 'nullable|date',
        ];
    }
}
