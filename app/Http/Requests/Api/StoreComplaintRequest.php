<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ];
    }

    public function messages(): array
    {
        $isEn = str_starts_with((string) $this->header('Accept-Language', 'ar'), 'en');
        return [
            'subject.required' => $isEn ? 'Subject is required'                    : 'موضوع الرسالة مطلوب',
            'subject.max'      => $isEn ? 'Subject may not exceed 255 characters'  : 'الموضوع يجب أن لا يتجاوز 255 حرفاً',
            'message.required' => $isEn ? 'Message is required'                    : 'نص الرسالة مطلوب',
            'message.max'      => $isEn ? 'Message may not exceed 5000 characters' : 'الرسالة يجب أن لا تتجاوز 5000 حرف',
        ];
    }
}