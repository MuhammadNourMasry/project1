<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string|max:1000'
        ];
    }
      public function messages(): array
    {
        return [
            'conversation_id.required' => 'Conversation ID is required',
            'conversation_id.exists' => 'Conversation not found',
            'message.required' => 'Message is required',
            'message.max' => 'Message is too long (max 1000 characters)'
        ];
    }
}
