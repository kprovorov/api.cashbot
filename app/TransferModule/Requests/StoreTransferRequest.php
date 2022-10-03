<?php

namespace App\TransferModule\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'jar_from_id' => 'required|integer|exists:jars,id',
            'jar_to_id' => 'required|integer|exists:jars,id',
        ];
    }
}
