<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SpecializationController extends Controller
{
    public function index(): JsonResponse
    {
        $specializations = Cache::remember(
            'api.specializations',
            86400, // 24 hours
            function (): array {
                return Specialization::where('is_active', true)
                    ->withCount('doctors')
                    ->orderBy('name_en')
                    ->get()
                    ->map(fn ($spec): array => [
                        'id' => $spec->id,
                        'name_en' => $spec->name_en,
                        'name_ar' => $spec->name_ar,
                        'icon' => $spec->icon,
                        'doctors_count' => $spec->doctors_count,
                    ])
                    ->all();
            }
        );

        return response()->json([
            'success' => true,
            'specializations' => $specializations,
        ]);
    }
}
