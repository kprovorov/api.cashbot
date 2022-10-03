<?php

namespace App\PaymentModule\Requests;

use App\AccountModule\Models\Jar;
use App\Enums\Currency;
use Illuminate\Database\Eloquent\Builder;
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
        $userJars = Jar::whereHas('account', function (Builder $query) {
            return $query->where('user_id', $this->user()->id);
        })->pluck('id');

        return [
            'jar_id'      => [
                'required',
                'integer',
                Rule::in($userJars),
            ],
            'description' => 'required|string|max:255',
            'amount'      => 'required|integer',
            'currency'    => ['required', new Enum(Currency::class)],
            'date'        => 'required|date',
            'hidden'      => 'required|boolean',
            'ends_on'     => 'nullable|date',
            'repeat'      => 'required|string|in:none,weekly,monthly,quarterly',
        ];
    }
}
