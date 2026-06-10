<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSize extends Model {
    protected $fillable = ['product_id','label','sort_order','is_available'];
    protected function casts(): array { return ['is_available' => 'boolean']; }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
