<?php
namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Profile update — every field is optional (`sometimes`). Email is allowed
 * but never required since accounts are phone-based.
 */
class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $userId = $this->user()->id;
        return [
            'name_ar'               => 'sometimes|required|string|max:255',
            'username'              => ['sometimes','required','string','max:100',
                                        "unique:users,username,{$userId}",
                                        'regex:/^[a-zA-Z0-9_.]+$/'],
            'phone'                 => ['sometimes','required','string',
                                        "unique:users,phone,{$userId}",
                                        'regex:/^(?:\+962|00962|0)7\d{8}$/'],
            // Email is optional and only validated for uniqueness when sent.
            'email'                 => ['sometimes','nullable','email',
                                        "unique:users,email,{$userId}"],
            'address'               => 'nullable|string',
            'region'                => 'nullable|string|max:100',
            'notifications_enabled' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        $isEn = str_starts_with((string) $this->header('Accept-Language', 'ar'), 'en');
        return [
            'phone.regex' => $isEn
                ? 'Please enter a valid Jordanian phone number (e.g. 0791234567 or +962791234567)'
                : 'يرجى إدخال رقم هاتف أردني صالح (مثال: 0791234567 أو +962791234567)',
            'phone.unique' => $isEn ? 'Phone number is already in use' : 'رقم الهاتف مستخدم مسبقاً',
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