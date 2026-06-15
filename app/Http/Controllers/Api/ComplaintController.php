<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplaintController extends ApiController
{
    /**
     * POST /api/v1/complaints
     * Auth: Required
     *
     * Saves a support complaint from the mobile app.
     * The user_id is taken from the authenticated token — never from the
     * request body — so a user can never file a complaint on behalf of
     * someone else.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $complaint = Complaint::create([
            'user_id' => $request->user()->id,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status'  => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رسالتك بنجاح. سيتواصل معك فريق الدعم قريباً.',
            'data'    => ['id' => $complaint->id],
        ], 201);
    }
}