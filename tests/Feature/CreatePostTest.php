<?php

namespace Tests\Feature;

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

        $this->get('admin/posts/create')
            ->assertSuccessful()
            ->assertSee('New post');

        $response = $this->post('admin/posts', [
            'title' => 'New post'
        ]);

        $response->assertStatus(201)->assertSee('Post created');

        $this->assertDatabaseHas('posts', [
            'title' => 'New post',
        ]);
    }

    /** @test */
    function authors_can_create_posts()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = $this->aUser());

        $user->assign('author');

        $this->get('admin/posts/create')
            ->assertSuccessful()
            ->assertSee('New post');

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
        $this->actingAs($user = $this->aUser());

        $this->get('admin/posts/create')
            ->assertStatus(403);

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
