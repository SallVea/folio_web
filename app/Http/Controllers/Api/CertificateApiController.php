<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => CertificateResource::collection($request->user()->certificates()->latest()->get()),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:150',
            'issuer'         => 'required|string|max:100',
            'issued_date'    => 'nullable|date',
            'expiry_date'    => 'nullable|date|after:issued_date',
            'credential_url' => 'nullable|url|max:255',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('certificates/'.$request->user()->id, 'public');
        }

        $cert = $request->user()->certificates()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sertifikat berhasil ditambahkan',
            'data'    => new CertificateResource($cert),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $cert      = $request->user()->certificates()->findOrFail($id);
        $validated = $request->validate([
            'title'          => 'sometimes|required|string|max:150',
            'issuer'         => 'sometimes|required|string|max:100',
            'issued_date'    => 'nullable|date',
            'expiry_date'    => 'nullable|date',
            'credential_url' => 'nullable|url|max:255',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($cert->image) Storage::disk('public')->delete($cert->image);
            $validated['image'] = $request->file('image')
                ->store('certificates/'.$request->user()->id, 'public');
        }

        $cert->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sertifikat berhasil diperbarui',
            'data'    => new CertificateResource($cert->fresh()),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $cert = $request->user()->certificates()->findOrFail($id);
        if ($cert->image) Storage::disk('public')->delete($cert->image);
        $cert->delete();

        return response()->json(['success' => true, 'message' => 'Sertifikat berhasil dihapus']);
    }
}
