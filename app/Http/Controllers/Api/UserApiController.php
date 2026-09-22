<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserApiController extends Controller
{
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => new UserResource(
                $request->user()->load(['portfolios', 'skills', 'certificates', 'socialLinks'])
            ),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'      => 'sometimes|required|string|max:100',
            'username'  => 'sometimes|required|string|max:50|alpha_dash|unique:users,username,'.$request->user()->id,
            'bio'       => 'nullable|string|max:500',
            'job_title' => 'nullable|string|max:100',
            'location'  => 'nullable|string|max:100',
            'website'   => 'nullable|url|max:255',
            'phone'     => 'nullable|string|max:20',
        ]);

        $request->user()->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data'    => new UserResource($request->user()->fresh()),
        ]);
    }

    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $user = $request->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('users/'.$user->id, 'public');
        $user->update(['profile_photo' => $path]);

        return response()->json([
            'success'   => true,
            'message'   => 'Foto profil berhasil diperbarui',
            'photo_url' => Storage::disk('public')->url($path),
        ]);
    }
}
