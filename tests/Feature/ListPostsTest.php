<?php

namespace Tests\Feature;

use App\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCollectionData;

class ListPostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function admins_can_see_all_the_posts()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($admin = $this->createAdmin());

        $post1 = factory(Post::class)->create();
        $post2 = factory(Post::class)->create();

        $response = $this->get('admin/posts');

        $response->assertStatus(200)
            ->assertViewIs('admin.posts.index')
            ->assertViewHas('posts', function ($posts) use ($post1, $post2) {
                return $posts->contains($post1) && $posts->contains($post2);
            });

        $this->assertNotRepeatedQueries();
    }

    /** @test */
    function authors_can_only_see_their_posts()
    {
        $this->withoutExceptionHandling();

        $user = $this->aUser();

        $post1ByCurrentUser = factory(Post::class)->create(['user_id' => $user->id]);
        $post2ByAnotherUser = factory(Post::class)->create();
        $post3ByCurrentUser = factory(Post::class)->create(['user_id' => $user->id]);
        $post4ByAnotherUser = factory(Post::class)->create();

        $this->actingAs($user);

        $response = $this->get('admin/posts');

        $response->assertStatus(200)
            ->assertViewIs('admin.posts.index');

        $response->assertViewCollection('posts')
            ->contains($post1ByCurrentUser)
            ->contains($post3ByCurrentUser)
            ->notContains($post2ByAnotherUser)
            ->notContains($post4ByAnotherUser);
    }
}
