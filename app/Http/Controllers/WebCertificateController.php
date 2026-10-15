<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Support\ImageOptimizer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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
            'credential_url' => 'nullable|url|starts_with:https://|max:255',
            'proof'          => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $validated['credential_status'] = $this->checkCredentialUrl($validated['credential_url'] ?? null);

        if (isset($validated['proof'])) {
            unset($validated['proof']);
        }

        $certificate = auth()->user()->certificates()->create($validated);

        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $path = $file->getMimeType() === 'application/pdf'
                ? $file->store('certificates/'.$certificate->id, 'public')
                : ImageOptimizer::store($file, 'certificates/'.$certificate->id);
            $certificate->update(['image' => $path]);
        }

        return back()->with('success', 'Sertifikat berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $certificate = auth()->user()->certificates()->findOrFail($id);

        $validated = $request->validate([
            'title'          => 'required|string|max:150',
            'issuer'         => 'required|string|max:100',
            'issued_date'    => 'nullable|date',
            'expiry_date'    => 'nullable|date|after:issued_date',
            'credential_url' => 'nullable|url|starts_with:https://|max:255',
        ]);

        $validated['credential_status'] = $this->checkCredentialUrl($validated['credential_url'] ?? null);
        $certificate->update($validated);

        return back()->with('success', 'Sertifikat berhasil diperbarui.');
    }

    public function uploadProof(Request $request, int $id): RedirectResponse
    {
        $certificate = auth()->user()->certificates()->findOrFail($id);

        $request->validate([
            'proof' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        if ($certificate->image) {
            Storage::disk('public')->delete($certificate->image);
        }

        $file = $request->file('proof');
        $path = $file->getMimeType() === 'application/pdf'
            ? $file->store('certificates/'.$certificate->id, 'public')
            : ImageOptimizer::store($file, 'certificates/'.$certificate->id);
        $certificate->update(['image' => $path]);

        return back()->with('success', 'Bukti sertifikat berhasil diupload.');
    }

    public function verifyCredential(int $id): RedirectResponse
    {
        $certificate = auth()->user()->certificates()->findOrFail($id);
        $certificate->update([
            'credential_status' => $this->checkCredentialUrl($certificate->credential_url),
        ]);

        return back()->with('success', 'Verifikasi kredensial dijalankan.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $certificate = auth()->user()->certificates()->findOrFail($id);

        if ($certificate->image) {
            Storage::disk('public')->delete($certificate->image);
        }

        $certificate->delete();

        return back()->with('success', 'Sertifikat berhasil dihapus.');
    }

    private function checkCredentialUrl(?string $url): string
    {
        if (blank($url)) {
            return 'unknown';
        }

        $parts = parse_url($url);
        $host = $parts['host'] ?? null;

        if (! $host || ! $this->hostIsPublic($host)) {
            return 'unknown';
        }

        // Hanya izinkan port standar HTTPS.
        if (isset($parts['port']) && $parts['port'] !== 443) {
            return 'unknown';
        }

        try {
            // withoutRedirecting mencegah SSRF via redirect ke jaringan internal.
            $response = Http::timeout(8)->withoutRedirecting()->get($url);

            if ($response->status() >= 300) {
                return $response->status() < 400 ? 'unknown' : 'invalid';
            }

            return $response->successful() ? 'valid' : 'invalid';
        } catch (\Throwable $e) {
            return 'invalid';
        }
    }

    private function hostIsPublic(string $host): bool
    {
        $host = strtolower(trim($host, '[]'));

        if ($host === '' || in_array($host, ['localhost', '127.0.0.1', '::1'], true) || str_ends_with($host, '.local')) {
            return false;
        }

        // Host berupa IP literal.
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $this->ipIsPublic($host);
        }

        // Resolusi DNS: semua alamat hasil resolusi harus publik.
        $resolved = gethostbynamel($host) ?: [];

        if (empty($resolved)) {
            return false;
        }

        foreach ($resolved as $ip) {
            if (! $this->ipIsPublic($ip)) {
                return false;
            }
        }

        return true;
    }

    private function ipIsPublic(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }
}