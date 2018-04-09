<?php

namespace Tests\Feature;

use App\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListPostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function authenticated_users_can_view_posts()
    {
        $this->withoutExceptionHandling();

        $post1 = factory(Post::class)->create();
        $post2 = factory(Post::class)->create();

        $this->actingAs($this->createUser());

        $response = $this->get('admin/posts');

        $response->assertStatus(200)
            ->assertViewIs('admin.posts.index')
            ->assertViewHas('posts', function ($posts) use ($post1, $post2) {
                return $posts->contains($post1) && $posts->contains($post2);
            });
    }
}
