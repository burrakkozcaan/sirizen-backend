<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportMessageRequest extends FormRequest
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
            'conversation_id' => ['nullable', 'integer', 'exists:crisp_conversations,id'],
            'content' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'conversation_id.exists' => 'Seçilen konuşma bulunamadı.',
            'content.required' => 'Mesaj boş olamaz.',
            'content.max' => 'Mesaj en fazla 2000 karakter olabilir.',
        ];
    }
}
