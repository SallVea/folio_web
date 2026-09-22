<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SocialLinkResource;
use App\Models\SocialLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialLinkApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => SocialLinkResource::collection($request->user()->socialLinks()->get()),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'platform' => 'required|in:'.implode(',', SocialLink::PLATFORMS),
            'url'      => 'required|url|max:255',
        ]);

        if ($request->user()->socialLinks()->where('platform', $validated['platform'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Platform '.$validated['platform'].' sudah ditambahkan',
            ], 422);
        }

        $link = $request->user()->socialLinks()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Link sosial media berhasil ditambahkan',
            'data'    => new SocialLinkResource($link),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $link      = $request->user()->socialLinks()->findOrFail($id);
        $validated = $request->validate([
            'platform' => 'sometimes|required|in:'.implode(',', SocialLink::PLATFORMS),
            'url'      => 'sometimes|required|url|max:255',
        ]);
        $link->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Link sosial media berhasil diperbarui',
            'data'    => new SocialLinkResource($link->fresh()),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $request->user()->socialLinks()->findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Link sosial media berhasil dihapus']);
    }
}
