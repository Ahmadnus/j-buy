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
        'name_ar' => 'required|max:255',
        'username' => 'required|max:100|unique:users,username',
        'phone' => 'required|unique:users,phone',
        'region' => 'required',
        'password' => 'required|min:6|confirmed',
    ];
}

   public function messages(): array
{
    return [
        'name_ar.required' => 'الاسم مطلوب',
        'username.required' => 'اسم المستخدم مطلوب',
        'username.unique' => 'اسم المستخدم مستخدم مسبقاً',
        'phone.required' => 'رقم الهاتف مطلوب',
        'phone.unique' => 'رقم الهاتف مسجل مسبقاً',
        'region.required' => 'المنطقة مطلوبة',
        'password.required' => 'كلمة المرور مطلوبة',
        'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        'password.confirmed' => 'تأكيد كلمة المرور غير مطابق',
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