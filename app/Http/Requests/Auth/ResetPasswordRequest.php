<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // otp: the 6-digit code the user received by email
            'otp'                   => 'required|string|min:6|max:6',
            'email'                 => 'required|email|exists:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'otp.required'      => 'رمز التحقق مطلوب',
            'otp.min'           => 'رمز التحقق يجب أن يكون 6 أرقام',
            'otp.max'           => 'رمز التحقق يجب أن يكون 6 أرقام',
            'email.exists'      => 'البريد الإلكتروني غير مسجل',
            'password.min'      => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed'=> 'كلمتا المرور غير متطابقتين',
        ];
    }
}
