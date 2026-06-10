<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductColor extends Model {
    protected $fillable = ['product_id','name_ar','name_en','hex_code','sort_order'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}