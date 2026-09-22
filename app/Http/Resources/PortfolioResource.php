<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PortfolioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'category'      => $this->category,
            'project_url'   => $this->project_url,
            'github_url'    => $this->github_url,
            'tech_stack'    => $this->tech_stack ?? [],
            'thumbnail_url' => $this->thumbnail
                ? Storage::disk('public')->url($this->thumbnail)
                : null,
            'is_featured'   => $this->is_featured,
            'views_count'   => $this->views_count ?? 0,
            'images'        => PortfolioImageResource::collection($this->whenLoaded('images')),
            'created_at'    => $this->created_at?->toDateTimeString(),
            'updated_at'    => $this->updated_at?->toDateTimeString(),
        ];
    }
}
