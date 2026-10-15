<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Support\ImageOptimizer;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WebPortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = $user->portfolios()->with('images')->orderBy('sort_order')->latest('id');

        if ($q = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', '%'.$q.'%')
                    ->orWhere('description', 'like', '%'.$q.'%')
                    ->orWhere('category', 'like', '%'.$q.'%')
                    ->orWhere('tech_stack', 'like', '%'.$q.'%');
            });
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        $portfolios = $query->latest()->get();
        $categories = $user->portfolios()->whereNotNull('category')->distinct()->pluck('category');

        return view('dashboard.portfolios', compact('portfolios', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);
        auth()->user()->portfolios()->create($validated);

        return back()->with('success', 'Portfolio berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $portfolio = auth()->user()->portfolios()->findOrFail($id);
        $portfolio->update($this->validateData($request));

        return back()->with('success', 'Portfolio berhasil diperbarui.');
    }

    public function reorder(Request $request): \Illuminate\Http\Response
    {
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        foreach ($ids['ids'] as $position => $id) {
            auth()->user()->portfolios()->findOrFail($id)->update(['sort_order' => $position]);
        }

        return response()->noContent();
    }

    public function duplicate(int $id): RedirectResponse
    {
        $portfolio = auth()->user()->portfolios()->findOrFail($id);

        $copy = $portfolio->replicate();
        $copy->title = $portfolio->title.' (copy)';
        $copy->sort_order = (int) auth()->user()->portfolios()->max('sort_order') + 1;
        $copy->save();

        return back()->with('success', 'Portfolio berhasil diduplikasi.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $portfolio = auth()->user()->portfolios()->with('images')->findOrFail($id);

        if ($portfolio->thumbnail) {
            Storage::disk('public')->delete($portfolio->thumbnail);
        }
        foreach ($portfolio->images as $img) {
            Storage::disk('public')->delete($img->image);
        }

        $portfolio->delete();

        return back()->with('success', 'Portfolio berhasil dihapus.');
    }

    public function uploadThumbnail(Request $request, int $id): RedirectResponse
    {
        $portfolio = auth()->user()->portfolios()->findOrFail($id);

        $request->validate(['thumbnail' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120']);

        if ($portfolio->thumbnail) {
            Storage::disk('public')->delete($portfolio->thumbnail);
        }

        $path = ImageOptimizer::store($request->file('thumbnail'), 'portfolios/'.$portfolio->id);
        $portfolio->update(['thumbnail' => $path]);

        return back()->with('success', 'Thumbnail berhasil diupload.');
    }

    public function storeImages(Request $request, int $id): RedirectResponse
    {
        $portfolio = auth()->user()->portfolios()->findOrFail($id);

        $request->validate([
            'images'   => 'required|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $startOrder = (int) ($portfolio->images()->max('order_index') ?? -1);

        foreach ($request->file('images') as $i => $file) {
            $path = ImageOptimizer::store($file, 'portfolios/'.$portfolio->id.'/gallery');
            $portfolio->images()->create([
                'image'       => $path,
                'order_index' => $startOrder + $i + 1,
            ]);
        }

        return back()->with('success', 'Foto galeri berhasil ditambahkan.');
    }

    public function destroyImage(int $id, int $imageId): RedirectResponse
    {
        $portfolio = auth()->user()->portfolios()->findOrFail($id);
        $image     = $portfolio->images()->findOrFail($imageId);

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:150',
            'description'      => 'nullable|string',
            'category'         => 'nullable|string|max:100',
            'project_url'      => 'nullable|url|starts_with:https://|max:255',
            'github_url'       => 'nullable|url|starts_with:https://|max:255',
            'tech_stack_input' => 'nullable|string',
            'is_featured'      => 'nullable|boolean',
        ]);

        $validated['tech_stack'] = $validated['tech_stack_input']
            ? array_filter(array_map('trim', explode(',', $validated['tech_stack_input'])))
            : [];
        unset($validated['tech_stack_input']);

        $validated['is_featured'] = $request->boolean('is_featured');

        return $validated;
    }
}
