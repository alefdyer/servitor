<?php

namespace App\Http\Requests;

use App\Models\Values\SubscriptionPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $deviceId
 * @property string $deviceModel
 * @property string $item
 * @property string $period
 */
class CreateOrderRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'deviceId' => 'required|uuid',
            'deviceModel' => 'required|string',
            'item' => [
                'required',
                Rule::in(['subscription']),
            ],
            'period' => [
                'required',
                Rule::enum(SubscriptionPeriod::class),
            ],
        ];
    }

    public function validationData(): array
    {
        return $this->json()->all();
    }
}
