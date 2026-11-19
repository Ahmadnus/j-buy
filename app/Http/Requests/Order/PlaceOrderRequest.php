<?php
namespace App\Http\Requests\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest {
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        // Older app builds don't send fulfillment_type — treat them as delivery.
        if (! $this->filled('fulfillment_type')) {
            $this->merge(['fulfillment_type' => 'delivery']);
        }
    }

    public function rules(): array {
        return [
            'full_name'              => 'required|string|max:255',
            'phone'                  => 'required|string|min:9',
            'fulfillment_type'       => 'required|in:delivery,pickup',

            // Delivery → city + address required. Pickup → a branch is required.
            'city'                   => 'required_if:fulfillment_type,delivery|nullable|string|max:255',
            'address'                => 'required_if:fulfillment_type,delivery|nullable|string',
            'branch_id'              => [
                'required_if:fulfillment_type,pickup', 'nullable', 'integer',
                Rule::exists('branches', 'id')->where('is_active', true),
            ],

            // Buyer's GPS location (optional, but sent by the app when granted)
            'latitude'               => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude'              => 'nullable|numeric|between:-180,180|required_with:latitude',

            'notes'                  => 'nullable|string|max:1000',
            'payment_method'         => 'required|in:cod',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|integer|exists:products,id',
            'items.*.selected_size'  => 'nullable|string',
            'items.*.selected_color' => 'nullable|string',
            'items.*.quantity'       => 'required|integer|min:1',
        ];
    }
}
