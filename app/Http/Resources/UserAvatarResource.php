<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAvatarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "avatar_url" => $this->avatar_url,
            "is_pro" => $this->is_pro,
            "name" => $this->whenNotNull($this->name),
            "is_admin" => $this->is_admin,
        ];
    }
}
