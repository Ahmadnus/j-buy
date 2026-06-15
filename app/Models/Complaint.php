<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    protected $fillable = ['user_id', 'subject', 'message', 'status', 'admin_notes'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime', 'updated_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['name_ar' => 'مستخدم محذوف']);
    }

    public function statusLabelAr(): string
    {
        return match($this->status) {
            'new'       => 'جديدة',
            'in_review' => 'قيد المراجعة',
            'resolved'  => 'تم الحل',
            'closed'    => 'مغلقة',
            default     => $this->status,
        };
    }
}