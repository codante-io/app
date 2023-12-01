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
            "name" => $this->whenNotNull($this->name),
            "badge" => $this->getBadgeName(),
        ];
    }

    private function getBadgeName()
    {
        if ($this->is_admin) {
            return "admin";
        }

        // if user dont want to show badge
        if ($this->settings && $this->settings["show_badge"] === false) {
            return null;
        }

        if ($this->is_pro) {
            return "pro";
        }
        return null;
    }
}
