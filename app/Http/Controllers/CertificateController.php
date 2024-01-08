<?php

namespace App\Http\Controllers;

use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use App\Models\Challenge;
use App\Models\ChallengeUser;
use App\Models\Tag;
use App\Models\User;
use App\Models\Workshop;
use App\Notifications\Discord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    // public function index()
    // {
    //     Auth::shouldUse("sanctum");
    //     return CertificateResource::collection(
    //         Certificate::query()
    //             ->where("user_id", auth()->id())
    //             ->get()
    //     );
    // }

    public function show($source, $slug)
    {
        Auth::shouldUse("sanctum");

        $source_id = $source === "challenge" ? Challenge::where('slug', $slug)->first()->id : Workshop::where('slug', $slug)->first()->id;

        $certificate = Certificate::query()
            ->where("user_id", auth()->id())
            ->where("source_type", $source)
            ->where($source === "challenge" ? "challenge_id" : "workshop_id", $source_id)
            ->firstOrFail();

        return new CertificateResource($certificate);
    }

    public function showById($id)
    {
        $certificate = Certificate::query()
            ->where("id", $id)
            ->firstOrFail();
        return new CertificateResource($certificate);
    }

    public function createRequestForCertificate(Request $request)
    {
        Auth::shouldUse("sanctum");

        $request->validate([
            "user_id" => "required|string",
            "source_type" => "required|in:workshop,challenge",
            "source_id" => "required|string",
        ]);

        $user = User::find($request->user_id);
        $source_id = $request->source_id;
        $source_type = $request->source_type;

        $certificateData = [
            'user_id' => $request->user_id,
            'source_type' => $source_type,
            'status' => $source_type === "workshop" ? "published" : "pending",
        ];

        if ($source_type === 'challenge') {
            $certificateData['challenge_id'] = $source_id;
            $source = Challenge::find($source_id);
            $challenge_user = ChallengeUser::where('challenge_id', $source->id)->where('user_id', $user->id)->first();
            $conclusion_date = $challenge_user->submitted_at ?? now()->format('Y-m-d H:i:s');
        } elseif ($source_type === 'workshop') {
            $certificateData['workshop_id'] = $source_id;
            $source = Workshop::find($source_id);
            $conclusion_date = now();
        }

        $tag_ids = $source->tags->pluck('id');
        $tag_names = Tag::whereIn('id', $tag_ids)->pluck('name');

        $certificateData['metadata'] = [
            [
                "duration" => $source_type === 'workshop' ? $source->duration_in_minutes : null,
                "source_name" => $source->name,
                "conclusion_date" => $conclusion_date,
                "tags" => $tag_names,
            ]
        ];

        $certificate = Certificate::create($certificateData);

        if ($source_type === "challenge") {
            new Discord(
                "💻 {$source->name}\n👤 {$user->name}\n🔗 Submissão: <https://codante.io/mini-projetos/{$source->slug}/submissoes/{$user->github_user}>\nPara aprovar, substitua o status para published: <https://api.codante.io/admin/certificate/{$certificate->id}/edit>",
                "pedidos-certificados",
            );
        }

        if ($source_type === "workshop") {
            new Discord(
                "💻 Workshop: {$source->name}\n👤 Certificado de Workshop gerado para {$user->name}",
                "pedidos-certificados",
            );
        }

        return $certificate;
    }
}
