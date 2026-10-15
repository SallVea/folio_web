<?php

namespace App\Http\Controllers;

use App\Rules\HttpUrl;
use App\Support\ImageOptimizer;
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
            'website'   => ['nullable', 'max:255', new HttpUrl()],
            'phone'     => 'nullable|string|max:20',
        ]);

        $validated['show_portfolio']    = $request->boolean('show_portfolio');
        $validated['show_skills']       = $request->boolean('show_skills');
        $validated['show_certificates'] = $request->boolean('show_certificates');

        $order = collect(explode(',', (string) $request->input('section_order')))
            ->map(fn ($v) => trim($v))
            ->filter(fn ($v) => in_array($v, ['portfolio', 'skills', 'certificates']))
            ->unique()
            ->values()
            ->all();

        foreach (['portfolio', 'skills', 'certificates'] as $section) {
            if (! in_array($section, $order)) {
                $order[] = $section;
            }
        }

        $validated['section_order'] = $order;

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

        $path = ImageOptimizer::store($request->file('photo'), 'users/'.$user->id, 800);
        $user->update(['profile_photo' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function updateUsername(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|min:3|max:30|alpha_dash|unique:users,username',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Username berhasil diperbarui.');
    }

    public function checkUsername(Request $request): \Illuminate\Http\JsonResponse
    {
        $username = (string) $request->query('username');
        $valid = preg_match('/^[A-Za-z0-9_-]{3,30}$/', $username) === 1;
        $taken = $valid && \App\Models\User::where('username', $username)->exists();

        return response()->json([
            'username' => $username,
            'valid' => $valid && ! $taken,
            'taken' => $taken,
        ]);
    }

    public function destroyAccount(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        auth()->logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Akun berhasil dihapus.');
    }

    public function storeSocialLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform' => 'required|in:GitHub,LinkedIn,Instagram,Twitter,YouTube,Facebook,Website,Other',
            'url'      => ['required', 'max:255', new HttpUrl()],
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
