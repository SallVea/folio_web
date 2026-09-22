<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WebSettingsController extends Controller
{
    public function index(): View
    {
        return view('dashboard.settings');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'bio'       => 'nullable|string|max:500',
            'job_title' => 'nullable|string|max:100',
            'location'  => 'nullable|string|max:100',
            'website'   => 'nullable|url|max:255',
            'phone'     => 'nullable|string|max:20',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function uploadPhoto(Request $request): RedirectResponse
    {
        $request->validate(['photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120']);

        $user = auth()->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('users/'.$user->id, 'public');
        $user->update(['profile_photo' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function storeSocialLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform' => 'required|in:GitHub,LinkedIn,Instagram,Twitter,YouTube,Facebook,Website,Other',
            'url'      => 'required|url|max:255',
        ]);

        auth()->user()->socialLinks()->create($validated);

        return back()->with('success', 'Link sosial media ditambahkan.');
    }

    public function destroySocialLink(int $id): RedirectResponse
    {
        auth()->user()->socialLinks()->findOrFail($id)->delete();

        return back()->with('success', 'Link sosial media dihapus.');
    }
}
