<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhoneModelResource;
use App\Http\Traits\ApiResponses;
use App\Models\PhoneModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhoneModelController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/phone-models
     *
     *   category      string  brand slug filter (e.g. "apple")
     *   search        string  model name search
     *   with_parts    bool    embed active parts (sorted by price) — default true
     *   parts_limit   int     max parts per model when embedded — default 6
     */
    public function index(Request $request): JsonResponse
    {
        $userId     = $request->user()?->id;
        $withParts  = $request->boolean('with_parts', true);
        $partsLimit = max(1, min(50, $request->integer('parts_limit', 6)));

        $query = PhoneModel::active()
            ->with('category')
            ->whereHas('category', fn ($q) => $q->where('is_active', true))
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->withMin(['products' => fn ($q) => $q->where('is_active', true)], 'price');

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn ($q) => $q->where('name_ar', 'like', "%{$term}%")
                                       ->orWhere('name_en', 'like', "%{$term}%"));
        }

        $models = $query->get();

        if ($withParts && $models->isNotEmpty()) {
            // One query for all parts, then trim per model — avoids N+1 and
            // works on every MySQL version (no window functions needed).
            $parts = \App\Models\Product::active()
                ->whereIn('phone_model_id', $models->pluck('id'))
                ->withFavoriteStatus($userId)
                ->orderBy('price')
                ->get()
                ->groupBy('phone_model_id');

            $models->each(fn ($m) => $m->setRelation(
                'products',
                ($parts[$m->id] ?? collect())->take($partsLimit)->values()
            ));
        }

        return $this->success(PhoneModelResource::collection($models));
    }

    /** GET /api/v1/phone-models/{id} — model with ALL of its active parts. */
    public function show(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()?->id;

        $model = PhoneModel::where('is_active', true)
            ->with(['category', 'products' => fn ($q) => $q->active()
                ->withFavoriteStatus($userId)
                ->orderBy('price')])
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->withMin(['products' => fn ($q) => $q->where('is_active', true)], 'price')
            ->find($id);

        if (! $model) {
            return $this->notFound();
        }

        return $this->success(new PhoneModelResource($model));
    }
}
