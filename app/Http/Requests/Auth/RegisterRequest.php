<?php
namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'name_ar'               => 'required|string|max:255',
            'username'              => ['required','string','max:100','unique:users','regex:/^[a-zA-Z0-9_.]+$/'],
            'email'                 => 'required|email|unique:users|max:255',
            'phone'                 => 'required|string|min:9',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ];
    }
    public function messages(): array {
        return [
            'username.regex'   => 'اسم المستخدم يجب أن يحتوي على حروف وأرقام ونقطة وشرطة سفلية فقط',
            'email.unique'     => 'البريد الإلكتروني مستخدم مسبقاً',
            'username.unique'  => 'اسم المستخدم مستخدم مسبقاً',
            'password.min'     => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين',
        ];
    }
}
