<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebCertificateController extends Controller
{
    public function index(): View
    {
        $certificates = auth()->user()->certificates()->latest()->get();

        return view('dashboard.certificates', compact('certificates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:150',
            'issuer'         => 'required|string|max:100',
            'issued_date'    => 'nullable|date',
            'expiry_date'    => 'nullable|date|after:issued_date',
            'credential_url' => 'nullable|url|max:255',
        ]);

        auth()->user()->certificates()->create($validated);

        return back()->with('success', 'Sertifikat berhasil ditambahkan.');
    }

    public function destroy(int $id): RedirectResponse
    {
        auth()->user()->certificates()->findOrFail($id)->delete();

        return back()->with('success', 'Sertifikat berhasil dihapus.');
    }
}
