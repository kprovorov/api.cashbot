<?php

namespace App\AccountModule\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJarRequest extends FormRequest
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
            'account_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'default' => 'required|boolean',
        ];
    }
}
