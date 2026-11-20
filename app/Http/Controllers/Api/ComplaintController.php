<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponses;
use App\Http\Requests\Api\StoreComplaintRequest;
use App\Models\Complaint;
use App\Models\ComplaintMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Customer side of support conversations.
 */
class ComplaintController extends Controller
{
    use ApiResponses;

    /**
     * POST /api/v1/complaints
     * Auth: optional — guests send contact_name + contact_phone.
     */
    public function store(StoreComplaintRequest $request): JsonResponse
    {
        $user = $request->user();

        $complaint = Complaint::create([
            'user_id'       => $user?->id,
            'contact_name'  => $user ? null : $request->validated('contact_name'),
            'contact_phone' => $user ? null : $request->validated('contact_phone'),
            'subject'       => $request->validated('subject'),
            'message'       => $request->validated('message'),
            'status'        => 'new',
        ]);
        $complaint->addMessage('customer', $request->validated('message'));

        $message = $this->localize(
            'تم إرسال رسالتك بنجاح. سيتواصل معك فريق الدعم قريباً.',
            'Your message was sent successfully. Support will contact you soon.'
        );

        return $this->created(['id' => $complaint->id], $message);
    }

    /**
     * GET /api/v1/complaints — the signed-in customer's conversations.
     */
    public function index(Request $request): JsonResponse
    {
        $complaints = Complaint::where('user_id', $request->user()->id)
            ->with('latestMessage')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Complaint $c) => $this->summary($c));

        return $this->success([
            'items'        => $complaints,
            'unread_count' => $complaints->where('has_unread_reply', true)->count(),
        ]);
    }

    /**
     * GET /api/v1/complaints/{id} — one conversation; marks replies as read.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $complaint = $this->ownedOrNull($request, $id);
        if (! $complaint) {
            return $this->notFound($this->localize('المحادثة غير موجودة', 'Conversation not found'));
        }

        if ($complaint->customer_unread) {
            $complaint->update(['customer_unread' => false]);
        }

        return $this->success($this->detail($complaint->load('messages')));
    }

    /**
     * POST /api/v1/complaints/{id}/messages — customer replies in the thread.
     */
    public function reply(Request $request, int $id): JsonResponse
    {
        $complaint = $this->ownedOrNull($request, $id);
        if (! $complaint) {
            return $this->notFound($this->localize('المحادثة غير موجودة', 'Conversation not found'));
        }

        if ($complaint->isClosed()) {
            return $this->error($this->localize(
                'هذه المحادثة مغلقة. يمكنك فتح طلب دعم جديد.',
                'This conversation is closed. You can open a new support request.'
            ), 422);
        }

        $data = $request->validate([
            'body' => 'required|string|max:5000',
        ], [
            'body.required' => $this->localize('نص الرسالة مطلوب', 'Message is required'),
            'body.max'      => $this->localize('الرسالة يجب أن لا تتجاوز 5000 حرف', 'Message may not exceed 5000 characters'),
        ]);

        $complaint->addMessage('customer', $data['body']);

        return $this->created($this->detail($complaint->fresh()->load('messages')));
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function ownedOrNull(Request $request, int $id): ?Complaint
    {
        return Complaint::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();
    }

    private function summary(Complaint $c): array
    {
        $last = $c->latestMessage;
        return [
            'id'               => $c->id,
            'subject'          => $c->subject,
            'status'           => $c->status,
            'status_label'     => $this->statusLabel($c->status),
            'has_unread_reply' => $c->customer_unread,
            'last_message'     => $last ? [
                'body'     => $last->body,
                'from'     => $last->sender,
                'sent_at'  => $last->created_at?->toIso8601String(),
            ] : null,
            'created_at'       => $c->created_at?->toIso8601String(),
            'last_message_at'  => ($c->last_message_at ?? $c->created_at)?->toIso8601String(),
        ];
    }

    private function detail(Complaint $c): array
    {
        return [
            'id'           => $c->id,
            'subject'      => $c->subject,
            'status'       => $c->status,
            'status_label' => $this->statusLabel($c->status),
            'can_reply'    => ! $c->isClosed(),
            'created_at'   => $c->created_at?->toIso8601String(),
            'messages'     => $c->messages->map(fn (ComplaintMessage $m) => [
                'id'      => $m->id,
                'from'    => $m->sender,
                'body'    => $m->body,
                'sent_at' => $m->created_at?->toIso8601String(),
            ])->values(),
        ];
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'new'       => $this->localize('بانتظار الرد', 'Awaiting reply'),
            'in_review' => $this->localize('قيد المتابعة', 'In progress'),
            'resolved'  => $this->localize('تم الحل', 'Resolved'),
            'closed'    => $this->localize('مغلقة', 'Closed'),
            default     => $status,
        };
    }
}
