<?php
namespace App\Http\Requests\Profile;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        $userId = $this->user()->id;
        return [
            'name_ar'  => 'required|string|max:255',
            'username' => ['required','string','max:100',
                           "unique:users,username,{$userId}",
                           'regex:/^[a-zA-Z0-9_.]+$/'],
            'phone'    => 'required|string|min:9',
            'email'    => "required|email|unique:users,email,{$userId}",
            'address'  => 'nullable|string',
        ];
    }
}
