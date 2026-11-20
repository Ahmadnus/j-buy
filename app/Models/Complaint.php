<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * A support conversation. `subject` + `message` hold the opening request (kept
 * for search and backwards compatibility); the full thread lives in
 * complaint_messages.
 */
class Complaint extends Model
{
    protected $fillable = [
        'user_id', 'contact_name', 'contact_phone',
        'subject', 'message', 'status', 'admin_notes',
        'admin_unread', 'customer_unread', 'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at'      => 'datetime',
            'updated_at'      => 'datetime',
            'last_message_at' => 'datetime',
            'admin_unread'    => 'boolean',
            'customer_unread' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['name_ar' => 'مستخدم محذوف']);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ComplaintMessage::class)->orderBy('created_at')->orderBy('id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ComplaintMessage::class)->latestOfMany();
    }

    public function isGuest(): bool
    {
        return $this->user_id === null && $this->contact_phone !== null;
    }

    /** Name to show staff: the account name, or what the guest typed. */
    public function contactName(): string
    {
        if ($this->user_id) {
            return (string) $this->user->name_ar;
        }
        return (string) ($this->contact_name ?: 'زائر');
    }

    /** Phone to call back: the account phone, or what the guest typed. */
    public function contactPhone(): ?string
    {
        return $this->user_id ? $this->user->phone : $this->contact_phone;
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /** Adds a message to the thread and updates unread flags / status. */
    public function addMessage(string $sender, string $body, ?int $adminId = null): ComplaintMessage
    {
        $msg = $this->messages()->create([
            'sender'   => $sender,
            'admin_id' => $adminId,
            'body'     => $body,
        ]);

        $updates = ['last_message_at' => $msg->created_at];

        if ($sender === 'admin') {
            $updates['customer_unread'] = true;
            $updates['admin_unread']    = false;
            // First staff reply moves a new ticket into review automatically.
            if ($this->status === 'new') {
                $updates['status'] = 'in_review';
            }
        } else {
            $updates['admin_unread'] = true;
            // A customer writing again on a solved ticket reopens it.
            if ($this->status === 'resolved') {
                $updates['status'] = 'in_review';
            }
        }

        $this->update($updates);

        return $msg;
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
