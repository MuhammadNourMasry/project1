<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartConversationRequest extends FormRequest
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
              'apartment_id' => 'required|exists:apartments,id',
            'message' => 'required|string|max:500'
        ];
    }
      public function messages(): array
    {
        return [
            'apartment_id.required' => 'Apartment ID is required',
            'apartment_id.exists' => 'Apartment not found',
            'message.required' => 'Message is required',
            'message.max' => 'Message is too long (max 500 characters)'
        ];
    }
}
