<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'issuer'         => $this->issuer,
            'issued_date'    => $this->issued_date?->toDateString(),
            'expiry_date'    => $this->expiry_date?->toDateString(),
            'credential_url' => $this->credential_url,
            'image_url'      => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,
            'created_at'     => $this->created_at?->toDateTimeString(),
        ];
    }
}
