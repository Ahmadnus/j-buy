<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Banner extends Model {
    protected $fillable = ['title_ar',
        'title_en','subtitle_ar',
        'subtitle_en','cta_text_ar',
        'cta_text_en','image_url','background_color',
                           'link_type','link_value','sort_order','is_active','starts_at','ends_at'];
    protected function casts(): array {
        return ['is_active'=>'boolean','starts_at'=>'datetime','ends_at'=>'datetime'];
    }

    public function scopeActive(Builder $query): Builder {
        return $query
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at','<=',now()))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at','>=',now()))
            ->orderBy('sort_order');
    }
}