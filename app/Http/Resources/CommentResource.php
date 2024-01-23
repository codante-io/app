<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user" => new UserResource($this->whenLoaded("user")),
            "challenge" => new ChallengeSummaryResource(
                $this->whenLoaded("challenge")
            ),
            "comment" => $this->comment,
            "replying_to" => $this->replying_to,
        ];
    }
}
