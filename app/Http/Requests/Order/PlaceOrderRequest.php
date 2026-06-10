<?php
namespace App\Http\Requests\Order;
use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'full_name'              => 'required|string|max:255',
            'phone'                  => 'required|string|min:9',
            'city'                   => 'required|string|max:255',
            'address'                => 'required|string',
            'notes'                  => 'nullable|string|max:1000',
            'payment_method'         => 'required|in:cod',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|integer|exists:products,id',
            'items.*.selected_size'  => 'required|string',
            'items.*.selected_color' => 'required|string',
            'items.*.quantity'       => 'required|integer|min:1',
        ];
    }
}
