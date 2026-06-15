<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponses;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UploadAvatarRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponses;

    public function show(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->only(
            'name_ar', 'username', 'phone', 'email', 'address',
            'region', 'notifications_enabled',
        ));

        return $this->success(new UserResource($user->fresh()));
    }

    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->addMediaFromRequest('avatar')
             ->toMediaCollection('avatars');

        $avatarUrl = $user->fresh()->avatar_url;

        return $this->success(['avatar_url' => $avatarUrl]);
    }
}