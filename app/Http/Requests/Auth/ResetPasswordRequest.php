<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'otp'      => 'required|string',
            'phone'    => ['required','string','exists:users,phone',
                           'regex:/^(?:\+962|00962|0)7\d{8}$/'],
            'password' => ['required','string','confirmed',
                           Password::min(8)->letters()->numbers()],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->phone) {
            $cleaned = preg_replace('/[\s\-]/', '', $this->phone);
            if (preg_match('/^07\d{8}$/', $cleaned)) {
                $cleaned = '+962' . substr($cleaned, 1);
            } elseif (str_starts_with($cleaned, '00962')) {
                $cleaned = '+' . substr($cleaned, 2);
            }
            $this->merge(['phone' => $cleaned]);
        }
    }
}