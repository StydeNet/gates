<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\{Post, User};
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function admins_can_update_posts()
    {
        // Arrange
        $admin = $this->createAdmin();

        $this->be($admin);

        $post = factory(Post::class)->create();

        // Act
        $result = Gate::allows('update-post', $post);
        //$result = $admin->can('update-post', $post);
        //$result = auth()->user()->can('update-post', $post);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    function authors_can_update_posts()
    {
        // Arrange
        $user = $this->createUser();

        $this->be($user);

        $post = factory(Post::class)->create([
            'user_id' => $user->id,
        ]);

        // Act
        $result = Gate::allows('update-post', $post);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    function unathorized_users_cannot_update_posts()
    {
        // Arrange
        $user = $this->createUser();

        $post = factory(Post::class)->create();

        // Act
        $result = Gate::forUser($user)->allows('update-post', $post);

        // Assert
        $this->assertFalse($result);
    }

    /** @test */
    function guests_cannot_update_posts()
    {
        // Arrange
        $post = factory(Post::class)->create();

        // Act
        $result = Gate::allows('update-post', $post);

        // Assert
        $this->assertFalse($result);
    }

    /** @test */
    function admin_can_delete_published_posts()
    {
        $admin = $this->createAdmin();

        $post = factory(Post::class)->states('published')->create();

        $this->assertTrue(
            Gate::forUser($admin)->allows('delete-post', $post)
        );
    }

    /** @test */
    function authors_can_delete_unpublished_posts()
    {
        $user = $this->createUser();

        $post = factory(Post::class)->states('draft')->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(
            Gate::forUser($user)->allows('delete-post', $post)
        );
    }

    /** @test */
    function authors_cannot_delete_published_posts()
    {
        $user = $this->createUser();

        $post = factory(Post::class)->states('published')->create([
            'user_id' => $user->id,
        ]);

        $this->assertFalse(
            Gate::forUser($user)->allows('delete-post', $post)
        );
    }
}
