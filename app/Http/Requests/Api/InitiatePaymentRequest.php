<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\PaymentProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InitiatePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'exists:orders,id'],
            'gateway' => ['required', Rule::enum(PaymentProvider::class)],
            'installment' => ['nullable', 'integer', 'min:1', 'max:12'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'order_id.required' => 'Sipariş ID zorunludur',
            'order_id.exists' => 'Geçersiz sipariş',
            'gateway.required' => 'Ödeme yöntemi seçilmelidir',
            'gateway.enum' => 'Geçersiz ödeme yöntemi',
            'installment.integer' => 'Taksit sayısı sayısal olmalıdır',
            'installment.min' => 'Taksit sayısı en az 1 olmalıdır',
            'installment.max' => 'Taksit sayısı en fazla 12 olabilir',
        ];
    }
}
