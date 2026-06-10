<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model {
    protected $fillable = ['user_id','product_id','name_ar','price','currency',
                           'image_url','selected_size','selected_color','quantity'];
    protected function casts(): array { return ['price' => 'decimal:2', 'quantity' => 'integer']; }

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class)->withDefault(); }

    public function getTotalPriceAttribute(): float {
        return round((float) $this->price * $this->quantity, 2);
    }
}
