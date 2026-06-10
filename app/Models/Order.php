<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'user_id',
        'customer_name', 'customer_phone', 'customer_city',
        'customer_address', 'customer_notes',
        'payment_method', 'status',
        'shipping_cost', 'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'status'        => OrderStatus::class,
            'shipping_cost' => 'decimal:2',
            'total_amount'  => 'decimal:2',
            'deleted_at'    => 'datetime',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['name_ar' => 'مستخدم محذوف']);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->latest('created_at');
    }

    // ── Computed accessors (used in OrderDetailResource) ──────────────────────

    public function getItemsCountAttribute(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function getProductsTotalAttribute(): float
    {
        return round((float) $this->total_amount - (float) $this->shipping_cost, 2);
    }

    // ── Business logic ────────────────────────────────────────────────────────

    /**
     * Transition to a new status and write an audit log entry.
     * Enforces OrderStatus::allowedTransitions().
     *
     * @throws \InvalidArgumentException
     */
    public function transitionTo(OrderStatus $newStatus, ?int $changedBy = null, ?string $note = null): void
    {
        if (! $this->status->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition order #{$this->order_number} from [{$this->status->value}] to [{$newStatus->value}]."
            );
        }

        $from = $this->status;
        $this->update(['status' => $newStatus]);

        $this->statusLogs()->create([
            'from_status' => $from->value,
            'to_status'   => $newStatus->value,
            'changed_by'  => $changedBy,
            'note'        => $note,
        ]);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['delivered', 'cancelled']);
    }
}
