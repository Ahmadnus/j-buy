<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Opening a support conversation. Signed-in customers send subject + message;
 * guests must also say who they are and how to reach them.
 */
class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $isGuest = $this->user() === null;

        return [
            'subject'       => 'required|string|max:255',
            'message'       => 'required|string|max:5000',
            'contact_name'  => [$isGuest ? 'required' : 'nullable', 'string', 'max:255'],
            'contact_phone' => [$isGuest ? 'required' : 'nullable', 'string', 'regex:/^\+9627\d{8}$/'],
        ];
    }

    /** Same phone normalization as registration/login (07… → +9627…). */
    protected function prepareForValidation(): void
    {
        if ($this->contact_phone) {
            $p = preg_replace('/[\s\-]/', '', (string) $this->contact_phone);
            if (preg_match('/^07\d{8}$/', $p)) {
                $p = '+962' . substr($p, 1);
            } elseif (str_starts_with($p, '00962')) {
                $p = '+' . substr($p, 2);
            }
            $this->merge(['contact_phone' => $p]);
        }
    }

    public function messages(): array
    {
        $isEn = str_starts_with((string) $this->header('Accept-Language', 'ar'), 'en');
        return [
            'subject.required'       => $isEn ? 'Subject is required'                    : 'موضوع الرسالة مطلوب',
            'subject.max'            => $isEn ? 'Subject may not exceed 255 characters'  : 'الموضوع يجب أن لا يتجاوز 255 حرفاً',
            'message.required'       => $isEn ? 'Message is required'                    : 'نص الرسالة مطلوب',
            'message.max'            => $isEn ? 'Message may not exceed 5000 characters' : 'الرسالة يجب أن لا تتجاوز 5000 حرف',
            'contact_name.required'  => $isEn ? 'Please enter your name'                 : 'يرجى إدخال اسمك',
            'contact_phone.required' => $isEn ? 'Please enter your phone number'         : 'يرجى إدخال رقم هاتفك',
            'contact_phone.regex'    => $isEn
                ? 'Please enter a valid Jordanian phone number (e.g. 0791234567)'
                : 'يرجى إدخال رقم هاتف أردني صالح (مثال: 0791234567)',
        ];
    }
}
