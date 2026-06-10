<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Phone-based login. Username and email are not accepted.
 */
class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'phone'    => ['required','string',
                           'regex:/^(?:\+962|00962|0)7\d{8}$/'],
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        $isEn = str_starts_with((string) $this->header('Accept-Language', 'ar'), 'en');
        return [
            'phone.regex' => $isEn
                ? 'Please enter a valid Jordanian phone number (e.g. 0791234567 or +962791234567)'
                : 'يرجى إدخال رقم هاتف أردني صالح (مثال: 0791234567 أو +962791234567)',
        ];
    }

    /**
     * Same normalization as registration so login works regardless of which
     * format the user typed.
     */
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