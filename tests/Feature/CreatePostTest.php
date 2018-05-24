<?php

namespace Tests\Feature;

use App\Post;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function admins_can_create_posts()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($admin = $this->createAdmin());

        $response = $this->post('admin/posts', [
            'title' => 'New post'
        ]);

        $response->assertStatus(201)->assertSee('Post created');

//        // Alternative: Test with Eloquent instead of the assertDatabaseHas helper:
//
//        tap(Post::first(), function ($post) {
//            $this->assertNotNull($post, 'The post was not created');
//
//            $this->assertSame('New post', $post->title);
//        });

        $this->assertDatabaseHas('posts', [
            'title' => 'New post',
        ]);
    }

    /** @test */
    function authors_can_create_posts()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = $this->createUser());

        $user->assign('author');

        BouncerFacade::allow('author')->to('create', Post::class);

        $response = $this->post('admin/posts', [
            'title' => 'New post'
        ]);

        $response->assertStatus(201)->assertSee('Post created');

        $this->assertDatabaseHas('posts', [
            'title' => 'New post',
        ]);
    }

    /** @test */
    function unathorized_users_cannot_create_posts()
    {
        $this->actingAs($user = $this->createUser());

        $response = $this->post('admin/posts', [
            'title' => 'New post'
        ]);

        $response->assertStatus(403);

//        $this->assertDatabaseMissing('posts', [
//            'title' => 'New post',
//        ]);

        // Alternative: Test with a custom database helper
        $this->assertDatabaseEmpty('posts');
    }
}
