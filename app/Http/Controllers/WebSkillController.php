<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebSkillController extends Controller
{
    public function index(): View
    {
        $skills = auth()->user()->skills()->get();

        return view('dashboard.skills', compact('skills'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'level'    => 'required|in:Beginner,Elementary,Intermediate,Advanced,Expert',
            'category' => 'nullable|string|max:50',
        ]);

        auth()->user()->skills()->create($validated);

        return back()->with('success', 'Skill berhasil ditambahkan.');
    }

    public function destroy(int $id): RedirectResponse
    {
        auth()->user()->skills()->findOrFail($id)->delete();

        return back()->with('success', 'Skill berhasil dihapus.');
    }
}
