<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'username'  => $this->username,
            'email'     => $this->email,
            'bio'       => $this->bio,
            'job_title' => $this->job_title,
            'location'  => $this->location,
            'website'   => $this->website,
            'phone'     => $this->phone,
            'photo_url' => $this->profile_photo
                ? Storage::disk('public')->url($this->profile_photo)
                : null,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
