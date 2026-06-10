<?php
namespace App\Http\Requests\Cart;
use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'product_id'     => 'required|integer|exists:products,id',
            'selected_size'  => 'required|string|max:20',
            'selected_color' => 'required|string|max:100',
            'quantity'       => 'required|integer|min:1',
        ];
    }
}
