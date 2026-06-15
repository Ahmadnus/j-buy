<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponses;
use App\Http\Requests\Api\StoreComplaintRequest;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;

class ComplaintController extends Controller
{
    use ApiResponses;

    /**
     * POST /api/v1/complaints
     * Auth: Required
     */
    public function store(StoreComplaintRequest $request): JsonResponse
    {
        $complaint = Complaint::create([
            'user_id' => $request->user()->id,
            'subject' => $request->validated('subject'),
            'message' => $request->validated('message'),
            'status'  => 'new',
        ]);

        $isEn    = str_starts_with((string) $request->header('Accept-Language', 'ar'), 'en');
        $message = $isEn
            ? 'Your message was sent successfully. Support will contact you soon.'
            : 'تم إرسال رسالتك بنجاح. سيتواصل معك فريق الدعم قريباً.';

        return $this->created(['id' => $complaint->id], $message);
    }
}