<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $this->assertDatabaseHas('posts', [
            'title' => 'New post',
        ]);
    }

    /** @test */
    function authors_can_create_posts()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = $this->createUser(['role' => 'author']));

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
        $this->actingAs($user = $this->createUser(['role' => 'subscriber']));

        $response = $this->post('admin/posts', [
            'title' => 'New post'
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('posts', [
            'title' => 'New post',
        ]);
    }
}
