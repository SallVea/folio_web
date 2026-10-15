<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebSkillController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = $user->skills()->with('levelHistories')->orderBy('sort_order')->orderBy('id');

        if ($q = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', '%'.$q.'%')
                    ->orWhere('category', 'like', '%'.$q.'%');
            });
        }

        if ($level = $request->query('level')) {
            $query->where('level', $level);
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        $skills = $query->get();
        $categories = $user->skills()->whereNotNull('category')->distinct()->pluck('category');

        return view('dashboard.skills', compact('skills', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'level'    => 'required|in:Beginner,Elementary,Intermediate,Advanced,Expert',
            'category' => 'nullable|string|max:50',
        ]);

        $skill = auth()->user()->skills()->create($validated);
        $skill->levelHistories()->create(['old_level' => null, 'new_level' => $validated['level']]);

        return back()->with('success', 'Skill berhasil ditambahkan.');
    }

    public function destroy(int $id): RedirectResponse
    {
        auth()->user()->skills()->findOrFail($id)->delete();

        return back()->with('success', 'Skill berhasil dihapus.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'level'    => 'required|in:Beginner,Elementary,Intermediate,Advanced,Expert',
            'category' => 'nullable|string|max:50',
        ]);

        $skill = auth()->user()->skills()->findOrFail($id);
        $oldLevel = $skill->level;
        $skill->update($validated);

        if ($oldLevel !== $validated['level']) {
            $skill->levelHistories()->create(['old_level' => $oldLevel, 'new_level' => $validated['level']]);
        }

        return back()->with('success', 'Skill berhasil diperbarui.');
    }

    public function move(Request $request, int $id): RedirectResponse
    {
        $user = auth()->user();
        $direction = $request->input('direction');
        $skills = $user->skills()->orderBy('sort_order')->orderBy('id')->get();
        $index = $skills->search(fn ($skill) => $skill->id === $id);

        if ($index === false) {
            abort(404);
        }

        foreach ($skills as $position => $skill) {
            if ($skill->sort_order !== $position) {
                $skill->update(['sort_order' => $position]);
            }
        }

        $skills = $user->skills()->orderBy('sort_order')->orderBy('id')->get();
        $index = $skills->search(fn ($skill) => $skill->id === $id);

        if ($direction === 'up' && $index > 0) {
            $current = $skills[$index];
            $previous = $skills[$index - 1];
            $current->update(['sort_order' => $previous->sort_order]);
            $previous->update(['sort_order' => $previous->sort_order + 1]);
        } elseif ($direction === 'down' && $index < $skills->count() - 1) {
            $current = $skills[$index];
            $next = $skills[$index + 1];
            $current->update(['sort_order' => $next->sort_order]);
            $next->update(['sort_order' => $next->sort_order - 1]);
        }

        return back()->with('success', 'Urutan skill diperbarui.');
    }

    public function reorder(\Illuminate\Http\Request $request): \Illuminate\Http\Response
    {
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        foreach ($ids['ids'] as $position => $id) {
            auth()->user()->skills()->findOrFail($id)->update(['sort_order' => $position]);
        }

        return response()->noContent();
    }

    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $skills = auth()->user()->skills()->orderBy('sort_order')->get(['name', 'level', 'category']);

        return response()->streamDownload(function () use ($skills) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['name', 'level', 'category']);
            foreach ($skills as $skill) {
                fputcsv($out, [$skill->name, $skill->level, $skill->category]);
            }
            fclose($out);
        }, 'skills.csv', ['Content-Type' => 'text/csv']);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt,json|max:2048']);

        $file = $request->file('file');
        $content = file_get_contents($file->getRealPath());
        $rows = [];

        if ($file->getClientOriginalExtension() === 'json') {
            $decoded = json_decode($content, true);
            $rows = is_array($decoded) ? $decoded : [];
        } else {
            $lines = array_filter(explode("\n", trim($content)));
            $header = str_getcsv(array_shift($lines));
            $headerCount = count($header);
            foreach ($lines as $line) {
                $row = array_slice(str_getcsv($line), 0, $headerCount);
                $row = array_pad($row, $headerCount, null);
                $rows[] = array_combine($header, $row);
            }
        }

        $count = 0;
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $skill = auth()->user()->skills()->create([
                'name' => $name,
                'level' => in_array($row['level'] ?? '', ['Beginner', 'Elementary', 'Intermediate', 'Advanced', 'Expert']) ? $row['level'] : 'Intermediate',
                'category' => $row['category'] ?? null,
            ]);
            $skill->levelHistories()->create(['old_level' => null, 'new_level' => $skill->level]);
            $count++;
        }

        return back()->with('success', $count.' skill berhasil diimpor.');
    }
}
