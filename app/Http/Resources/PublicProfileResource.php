<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PublicProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name'         => $this->name,
            'username'     => $this->username,
            'bio'          => $this->bio,
            'job_title'    => $this->job_title,
            'location'     => $this->location,
            'website'      => $this->website,
            'photo_url'    => $this->profile_photo
                ? Storage::disk('public')->url($this->profile_photo)
                : null,
            'portfolios'   => PortfolioResource::collection($this->whenLoaded('portfolios')),
            'skills'       => SkillResource::collection($this->whenLoaded('skills')),
            'certificates' => CertificateResource::collection($this->whenLoaded('certificates')),
            'social_links' => SocialLinkResource::collection($this->whenLoaded('socialLinks')),
        ];
    }
}
