<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Phone-based registration.
 *
 * Required fields: name_ar, username, phone, region, password.
 * No email — email-based registration has been removed.
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name_ar'  => 'required|string|max:255',
            'username' => ['required','string','max:100','unique:users','regex:/^[a-zA-Z0-9_.]+$/'],

            // Jordanian phone — UNIQUE so the same number can't sign up twice.
            //   07XXXXXXXX | +9627XXXXXXXX | 009627XXXXXXXX
            'phone'    => ['required','string','unique:users,phone',
                           'regex:/^(?:\+962|00962|0)7\d{8}$/'],

            'region'   => 'required|string|max:100',

            'password' => ['required','string','confirmed',
                           Password::min(8)->letters()->numbers()],
            'password_confirmation' => 'required|string',
        ];
    }

    public function messages(): array
    {
        $isEn = str_starts_with((string) $this->header('Accept-Language', 'ar'), 'en');

        return [
            'username.regex'     => $isEn
                ? 'Username may contain only letters, numbers, dots and underscores'
                : 'اسم المستخدم يجب أن يحتوي على حروف وأرقام ونقطة وشرطة سفلية فقط',
            'username.unique'    => $isEn ? 'Username is already taken' : 'اسم المستخدم مستخدم مسبقاً',
            'phone.unique'       => $isEn ? 'Phone number is already registered' : 'رقم الهاتف مسجّل مسبقاً',
            'phone.regex'        => $isEn
                ? 'Please enter a valid Jordanian phone number (e.g. 0791234567 or +962791234567)'
                : 'يرجى إدخال رقم هاتف أردني صالح (مثال: 0791234567 أو +962791234567)',
            'phone.required'     => $isEn ? 'Phone number is required'    : 'رقم الهاتف مطلوب',
            'region.required'    => $isEn ? 'Region is required'          : 'المنطقة مطلوبة',
            'password.min'       => $isEn ? 'Password must be at least 8 characters' : 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => $isEn ? 'Passwords do not match'      : 'كلمتا المرور غير متطابقتين',
        ];
    }

    /**
     * Normalize the phone to a canonical international form so the same
     * number written three different ways doesn't create three accounts.
     */
    protected function prepareForValidation(): void
    {
        if ($this->phone) {
            $this->merge(['phone' => $this->normalizePhone($this->phone)]);
        }
    }

    private function normalizePhone(string $raw): string
    {
        // Strip spaces and dashes for tolerance
        $cleaned = preg_replace('/[\s\-]/', '', $raw);
        // 0791234567 → +962791234567
        if (preg_match('/^07\d{8}$/', $cleaned)) {
            return '+962' . substr($cleaned, 1);
        }
        // 00962791234567 → +962791234567
        if (str_starts_with($cleaned, '00962')) {
            return '+' . substr($cleaned, 2);
        }
        return $cleaned;
    }
}