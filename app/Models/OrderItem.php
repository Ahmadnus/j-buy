<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model {
    protected $fillable = ['order_id','product_id','name_ar','name_en','price','currency',
                           'image_url','selected_size','selected_color','quantity'];
    protected function casts(): array { return ['price' => 'decimal:2', 'quantity' => 'integer']; }

    public function order(): BelongsTo   { return $this->belongsTo(Order::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class)->withDefault(); }

    public function getLineTotalAttribute(): float {
        return round((float) $this->price * $this->quantity, 2);
    }
}
