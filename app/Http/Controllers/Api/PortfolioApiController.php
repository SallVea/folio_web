<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortfolioImageResource;
use App\Http\Resources\PortfolioResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $portfolios = $request->user()->portfolios()->with('images')->latest()->paginate(12);

        return response()->json([
            'success' => true,
            'data'    => PortfolioResource::collection($portfolios),
            'meta'    => [
                'current_page' => $portfolios->currentPage(),
                'last_page'    => $portfolios->lastPage(),
                'total'        => $portfolios->total(),
                'per_page'     => $portfolios->perPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:150',
            'description'  => 'nullable|string',
            'category'     => 'nullable|string|max:100',
            'project_url'  => 'nullable|url|max:255',
            'github_url'   => 'nullable|url|max:255',
            'tech_stack'   => 'nullable|array',
            'tech_stack.*'=> 'string|max:50',
            'is_featured'  => 'boolean',
        ]);

        $portfolio = $request->user()->portfolios()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Portfolio berhasil dibuat',
            'data'    => new PortfolioResource($portfolio),
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $portfolio = $request->user()->portfolios()->with('images')->findOrFail($id);

        return response()->json(['success' => true, 'data' => new PortfolioResource($portfolio)]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $portfolio = $request->user()->portfolios()->findOrFail($id);

        $validated = $request->validate([
            'title'        => 'sometimes|required|string|max:150',
            'description'  => 'nullable|string',
            'category'     => 'nullable|string|max:100',
            'project_url'  => 'nullable|url|max:255',
            'github_url'   => 'nullable|url|max:255',
            'tech_stack'   => 'nullable|array',
            'tech_stack.*'=> 'string|max:50',
            'is_featured'  => 'boolean',
        ]);

        $portfolio->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Portfolio berhasil diperbarui',
            'data'    => new PortfolioResource($portfolio->fresh()->load('images')),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $portfolio = $request->user()->portfolios()->with('images')->findOrFail($id);

        if ($portfolio->thumbnail) {
            Storage::disk('public')->delete($portfolio->thumbnail);
        }

        foreach ($portfolio->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $portfolio->delete();

        return response()->json(['success' => true, 'message' => 'Portfolio berhasil dihapus']);
    }

    public function uploadThumbnail(Request $request, int $id): JsonResponse
    {
        $portfolio = $request->user()->portfolios()->findOrFail($id);

        $request->validate(['thumbnail' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120']);

        if ($portfolio->thumbnail) {
            Storage::disk('public')->delete($portfolio->thumbnail);
        }

        $path = $request->file('thumbnail')->store('portfolios/'.$portfolio->id, 'public');
        $portfolio->update(['thumbnail' => $path]);

        return response()->json([
            'success'       => true,
            'message'       => 'Thumbnail berhasil diupload',
            'thumbnail_url' => Storage::disk('public')->url($path),
        ]);
    }

    /**
     * Upload satu atau beberapa gambar galeri sekaligus.
     * Form-data field: images[] (array of files), max 10 file per request.
     */
    public function uploadImages(Request $request, int $id): JsonResponse
    {
        $portfolio = $request->user()->portfolios()->findOrFail($id);

        $request->validate([
            'images'   => 'required|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $startOrder = (int) ($portfolio->images()->max('order_index') ?? -1);

        $created = [];
        foreach ($request->file('images') as $i => $file) {
            $path = $file->store('portfolios/'.$portfolio->id.'/gallery', 'public');
            $created[] = $portfolio->images()->create([
                'image'       => $path,
                'order_index' => $startOrder + $i + 1,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($created).' gambar berhasil ditambahkan',
            'data'    => PortfolioImageResource::collection($created),
        ], 201);
    }

    /**
     * Hapus satu gambar dari galeri portfolio.
     */
    public function deleteImage(Request $request, int $id, int $imageId): JsonResponse
    {
        $portfolio = $request->user()->portfolios()->findOrFail($id);
        $image     = $portfolio->images()->findOrFail($imageId);

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gambar berhasil dihapus',
        ]);
    }
}
