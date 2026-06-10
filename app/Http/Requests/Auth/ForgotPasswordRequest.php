<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'phone' => ['required','string','exists:users,phone',
                        'regex:/^(?:\+962|00962|0)7\d{8}$/'],
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