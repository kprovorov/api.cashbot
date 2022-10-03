<?php

namespace App\TransferModule\Requests;

use App\AccountModule\Models\Jar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $userJars = Jar::whereHas('account', function (Builder $query) {
            return $query->where('user_id', $this->user()->id);
        })->pluck('id');

        return [
            'jar_from_id' => [
                'required',
                'integer',
                Rule::in($userJars),
            ],
            'jar_to_id'   => [
                'required',
                'integer',
                Rule::in($userJars),
            ],
        ];
    }
}
