<?php

namespace Tests\Feature;

use App\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeletePostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function admins_can_delete_posts()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin);

        $post = factory(Post::class)->create();

        $this->delete("admin/posts/{$post->id}")
            ->assertRedirect('admin/posts')
            ->assertDontSee($post->title);

        //$this->assertDatabaseEmpty('posts');

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id
        ]);
    }

    /** @test */
    function editors_can_delete_drafts()
    {
        $editor = $this->aUser();

        $editor->assign('editor');

        $this->actingAs($editor);

        $post = factory(Post::class)->create([
            'status' => 'draft'
        ]);

        $this->delete("admin/posts/{$post->id}")
            ->assertRedirect('admin/posts')
            ->assertDontSee($post->title);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id
        ]);
    }

    /** @test */
    function editors_cannot_delete_published_posts()
    {
        $editor = $this->aUser();

        $editor->assign('editor');

        $this->actingAs($editor);

        $post = factory(Post::class)->create([
            'status' => 'published'
        ]);

        $this->delete("admin/posts/{$post->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id
        ]);
    }

    /** @test */
    function authors_can_delete_drafts_they_own()
    {
        $author = $this->aUser();

        $author->assign('author');

        $this->actingAs($author);

        $post = factory(Post::class)->create([
            'status' => 'draft',
            'user_id' => $author->id,
        ]);

        $this->delete("admin/posts/{$post->id}")
            ->assertRedirect('admin/posts')
            ->assertDontSee($post->title);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id
        ]);
    }

    /** @test */
    function authors_cannot_delete_drafts_they_dont_own()
    {
        $post = factory(Post::class)->create([
            'status' => 'draft'
        ]);

        $author = $this->aUser();

        $author->assign('author');

        $this->actingAs($author);

        $this->delete("admin/posts/{$post->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id
        ]);
    }

    /** @test */
    function authors_cannot_delete_published_posts()
    {
        $author = $this->aUser();

        $author->assign('author');

        $post = factory(Post::class)->create([
            'user_id' => $author->id,
            'status' => 'published',
        ]);

        $this->actingAs($author);

        $this->delete("admin/posts/{$post->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id
        ]);
    }

    /** @test */
    function unauthorized_users_cannot_delete_posts()
    {
        $user = $this->aUser();

        $post = factory(Post::class)->create([
            'user_id' => $user->id,
            'status' => 'draft',
        ]);

        $this->actingAs($user);

        $this->delete("admin/posts/{$post->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id
        ]);
    }

    /** @test */
    function guests_cannot_delete_posts()
    {
        $post = factory(Post::class)->create([
            'status' => 'draft',
        ]);

        $this->delete("admin/posts/{$post->id}")
            ->assertRedirect('login');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id
        ]);
    }
}
