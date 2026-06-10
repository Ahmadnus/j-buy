<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Base controller providing the exact response envelope from the API spec:
 *   { "success": true, "message": "...", "data": { } }
 */
abstract class ApiController extends Controller
{
    protected function success(mixed $data = null, string $message = '', int $status = 200): JsonResponse
    {
        $payload = ['success' => true];
        if ($message)  $payload['message'] = $message;
        if ($data !== null) $payload['data'] = $data;

        return response()->json($payload, $status);
    }

    protected function created(mixed $data, string $message = ''): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    protected function error(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        $payload = ['success' => false, 'message' => $message];
        if ($errors) $payload['errors'] = $errors;

        return response()->json($payload, $status);
    }

    protected function notFound(string $message = 'المورد المطلوب غير موجود'): JsonResponse
    {
        return $this->error($message, 404);
    }

    protected function unauthorized(string $message = 'يجب تسجيل الدخول أولاً'): JsonResponse
    {
        return $this->error($message, 401);
    }

    protected function forbidden(string $message = 'غير مصرح لك بهذا الإجراء'): JsonResponse
    {
        return $this->error($message, 403);
    }
}
