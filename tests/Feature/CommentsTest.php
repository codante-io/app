<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_cannot_comment_if_not_logged_in(): void
    {
        $response = $this->postJson("/api/comments");
        $response->assertStatus(401);
    }

    /** @test */
    public function it_cannot_comment_if_no_parameters(): void
    {
        $token = $this->signInAndReturnToken();

        $response = $this->postJson(
            "/api/comments",
            [],
            [
                "Authorization" => "Bearer $token",
            ]
        );
        $response->assertStatus(422);

        // assert message
        $response->assertJsonValidationErrors("commentable_type");
        $response->assertJsonValidationErrors("commentable_id");
        $response->assertJsonValidationErrors("comment");
    }

    /** @test */
    public function it_cannot_comment_if_no_commentable_id(): void
    {
        $token = $this->signInAndReturnToken();

        $response = $this->postJson(
            "/api/comments",
            [
                "commentable_type" => "ChallengeUser",
                "comment" => "Very good!",
            ],
            [
                "Authorization" => "Bearer $token",
            ]
        );
        $response->assertStatus(422);

        // assert message
        $response->assertJsonValidationErrors("commentable_id");
    }

    /** @test */
    public function it_cannot_comment_if_invalid_commentable_id(): void
    {
        $token = $this->signInAndReturnToken();

        $response = $this->postJson(
            "/api/comments",
            [
                "commentable_type" => "ChallengeUser",
                "comment" => "Very good!",
                "commentable_id" => "1",
            ],
            [
                "Authorization" => "Bearer $token",
            ]
        );
        $response->assertStatus(404);
    }

    // /** @test */
    // public function it_cannot_comment_if_no_commentable_type(): void
    // {
    //     $token = $this->signInAndReturnToken();

    //     $response = $this->postJson(
    //         "/api/comments",
    //         [
    //             "commentable_id" => 1,
    //             "comment" => "Very good!",
    //         ],
    //         [
    //             "Authorization" => "Bearer $token",
    //         ]
    //     );
    //     $response->assertStatus(422);

    //     // assert message
    //     $response->assertJsonValidationErrors("commentable_type");
    // }

    // /** @test */
    // public function it_cannot_comment_if_no_comment(): void
    // {
    //     $token = $this->signInAndReturnToken();

    //     $response = $this->postJson(
    //         "/api/comments",
    //         [
    //             "commentable_id" => 1,
    //             "commentable_type" => "App\Models\BlogPost",
    //         ],
    //         [
    //             "Authorization" => "Bearer $token",
    //         ]
    //     );
    //     $response->assertStatus(422);

    //     // assert message
    //     $response->assertJsonValidationErrors("comment");
    // }

    // /** @test */
    // public function it_cannot_comment_if_invalid_commentable_type(): void
    // {
    //     $token = $this->signInAndReturnToken();

    //     $response = $this->postJson(
    //         "/api/comments",
    //         [
    //             "commentable_id" => 1,
    //             "commentable_type" => "InvalidModel",
    //             "comment" => "Very good!",
    //         ],
    //         [
    //             "Authorization" => "Bearer $token",
    //         ]
    //     );
    //     $response->assertStatus(422);
    //     $response->assertJsonValidationErrors("commentable_type");
    //     $response->assertJsonFragment([
    //         "commentable_type" => ["Commentable model does not exist."],
    //     ]);
    // }

    // /** @test */
    // public function it_cannot_comment_if_model_is_not_commentable()
    // {
    //     $token = $this->signInAndReturnToken();

    //     $response = $this->postJson(
    //         "/api/comments",
    //         [
    //             "commentable_id" => 1,
    //             "commentable_type" => "User",
    //             "comment" => "Very good!",
    //         ],
    //         [
    //             "Authorization" => "Bearer $token",
    //         ]
    //     );
    //     $response->assertStatus(422);
    //     $response->assertJsonValidationErrors("commentable_type");

    //     // message should be Model is not commentable
    //     $response->assertJsonFragment([
    //         "commentable_type" => ["Model is not commentable."],
    //     ]);
    // }

    // /** @test */
    // public function it_can_comment(): void
    // {
    //     $token = $this->signInAndReturnToken();

    //     $blogPost = \App\Models\BlogPost::factory()->create([
    //         "status" => "published",
    //     ]);

    //     $response = $this->postJson(
    //         "/api/comments",
    //         [
    //             "commentable_id" => $blogPost->id,
    //             "commentable_type" => "BlogPost",
    //             "comment" => "Very good!",
    //         ],
    //         [
    //             "Authorization" => "Bearer $token",
    //         ]
    //     );
    //     $response->assertStatus(201);

    //     // assert comment
    //     $this->assertDatabaseHas("comments", [
    //         "commentable_id" => $blogPost->id,
    //         "commentable_type" => "App\Models\BlogPost",
    //         "comment" => "Very good!",
    //     ]);
    // }

    // /** @test */
    // public function it_can_uncomment()
    // {
    //     $user = \App\Models\User::factory()->create([
    //         "password" => bcrypt("password"),
    //     ]);

    //     $token = $this->signInAndReturnToken($user);

    //     $blogPost = \App\Models\BlogPost::factory()->create([
    //         "status" => "published",
    //     ]);

    //     $comment = \App\Models\Comment::factory()->create([
    //         "commentable_id" => $blogPost->id,
    //         "commentable_type" => "App\Models\BlogPost",
    //         "comment" => "Very good!",
    //         "user_id" => $user->id,
    //     ]);

    //     $comments = \App\Models\Comment::all();
    //     $this->assertCount(1, $comments);

    //     $response = $this->postJson(
    //         "/api/comments",
    //         [
    //             "commentable_id" => $blogPost->id,
    //             "commentable_type" => "BlogPost",
    //             "comment" => "Very good!",
    //         ],
    //         [
    //             "Authorization" => "Bearer $token",
    //         ]
    //     );
    //     $response->assertStatus(204);

    //     $comments = \App\Models\Comment::all();
    //     $this->assertCount(0, $comments);
    // }
}
