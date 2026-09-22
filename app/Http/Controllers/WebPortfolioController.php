<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WebPortfolioController extends Controller
{
    public function index(): View
    {
        $portfolios = auth()->user()->portfolios()->with('images')->latest()->get();

        return view('dashboard.portfolios', compact('portfolios'));
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

        $path = $request->file('thumbnail')->store('portfolios/'.$portfolio->id, 'public');
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
            $path = $file->store('portfolios/'.$portfolio->id.'/gallery', 'public');
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
            'project_url'      => 'nullable|url|max:255',
            'github_url'       => 'nullable|url|max:255',
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
