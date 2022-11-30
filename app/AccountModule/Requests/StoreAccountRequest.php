<?php

namespace App\AccountModule\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'currency' => 'required|string|max:255',
            'balance' => 'required|integer',
            'external_id' => 'nullable|string|max:255',
            'provider' => 'nullable|string|max:255',
        ];
    }
}