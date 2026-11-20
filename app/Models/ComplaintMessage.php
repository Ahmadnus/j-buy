<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One message in a support conversation — from the customer or from support.
 */
class ComplaintMessage extends Model
{
    protected $fillable = ['complaint_id', 'sender', 'admin_id', 'body'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime', 'updated_at' => 'datetime'];
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function isFromAdmin(): bool
    {
        return $this->sender === 'admin';
    }
}
