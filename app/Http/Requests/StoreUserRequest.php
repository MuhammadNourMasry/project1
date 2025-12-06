<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
        'phone'=>'required|digits:10|unique:users,phone',
        'first_name' =>'required|string|max:15|regex:/^[a-zA-Z\s]+$/',
        'last_name'=>'required|string|max:15|regex:/^[a-zA-Z\s]+$/',
        'date_of_birth'=>'required|date|before:today',
        'email'=>'required|unique:users,email|ends_with:@gmail.com',
        'bio'=>'nullable|string|max:500',
        'role'=>'required|in:tenant,rented',
        'personal_photo'=>'required|image|mimes:png,jpg,jpeg|max:2048',
        'photo_of_personal_ID'=>'required|image|mimes:png,jpg,jpeg|max:2048',
        'password' =>[
                      'required',
                      'string',
                      'min:8',
                      'regex:/[a-z]/',
                      'regex:/[A-Z]/',
                      'regex:/[0-9]/',
                     'regex:/[@$!%*#?&]/',
                     'confirmed',
            ]
       ];
    }
 /*public function messages(): array
{
    return [
        'phone.required' => 'رقم الهاتف مطلوب.',
        'phone.digits' => 'رقم الهاتف يجب أن يتكون من 10 أرقام بالضبط.',
        'phone.unique' => 'هذا الرقم مستخدم مسبقاً.',
        'password.required' => 'كلمة المرور مطلوبة.',
        'password.min' => 'يجب أن تكون كلمة المرور على الأقل 8 أحرف.',
        'password.regex' => 'يجب أن تحتوي كلمة المرور على حرف كبير، حرف صغير، رقم، ورمز خاص.',
    ];
}*/
}
