<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UploadAvatarRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends ApiController
{
    // ── GET /profile ──────────────────────────────────────────────────────────

    public function show(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }

    // ── PUT /profile ──────────────────────────────────────────────────────────

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update($request->only('name_ar', 'username', 'phone', 'email', 'address'));

        return $this->success(new UserResource($user->fresh()));
    }

    // ── POST /profile/avatar ──────────────────────────────────────────────────

    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $user = $request->user();

        // Spatie Media Library replaces any existing avatar (singleFile collection)
        $user->addMediaFromRequest('avatar')
             ->toMediaCollection('avatars');

        // avatar_url accessor on User returns Media Library URL
        $avatarUrl = $user->fresh()->avatar_url;

        return $this->success(['avatar_url' => $avatarUrl]);
    }
}
