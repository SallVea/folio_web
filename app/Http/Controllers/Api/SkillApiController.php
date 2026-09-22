<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => SkillResource::collection($request->user()->skills()->get()),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'level'    => 'required|in:Beginner,Elementary,Intermediate,Advanced,Expert',
            'category' => 'nullable|string|max:50',
        ]);

        $skill = $request->user()->skills()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Skill berhasil ditambahkan',
            'data'    => new SkillResource($skill),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $skill     = $request->user()->skills()->findOrFail($id);
        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:100',
            'level'    => 'sometimes|required|in:Beginner,Elementary,Intermediate,Advanced,Expert',
            'category' => 'nullable|string|max:50',
        ]);
        $skill->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Skill berhasil diperbarui',
            'data'    => new SkillResource($skill->fresh()),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $request->user()->skills()->findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Skill berhasil dihapus']);
    }
}
