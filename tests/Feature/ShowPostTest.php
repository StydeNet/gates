<?php

namespace Tests\Feature;

use App\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowPostTest extends TestCase
{
    use RefreshDatabase;

    protected $post;

    public function setUp()
    {
        parent::setUp();

        $this->post = factory(Post::class)->create([
            'teaser' => 'The teaser',
            'content' => 'The content of the post',
        ]);
    }

    /** @test */
    function anonymous_users_can_only_see_the_content_of_the_post_after_accepting_the_terms()
    {
        $this->withoutExceptionHandling();
        //$this->withoutMiddleware(EncryptCookies::class);

        $this->get($this->postUrl())
            ->assertStatus(200)
            ->assertSee('The teaser')
            ->assertDontSee('The content of the post');

        $this->call('GET', $this->postUrl(), [], ['accept_terms' => 1])
            ->assertStatus(200)
            ->assertSee('The content of the post');
    }

    /** @test */
    function logged_in_users_can_always_see_the_content_of_the_posts()
    {
        $this->actingAs($this->createUser());

        $response = $this->get($this->postUrl());

        $response->assertStatus(200)
            ->assertSee('The teaser')
            ->assertSee('The content of the post');
    }

    protected function postUrl()
    {
        return "posts/{$this->post->id}";
    }

    protected function withTermsAccepted()
    {
        return ['accept_terms' => encrypt(1)];
    }

    /**
     * @param array $cookies
     * @return \Tests\TestCase
     */
    protected function withCookies(array $cookies)
    {
        return new RequestWithCookies($this, $cookies);
    }
}

class RequestWithCookies
{
    protected $test;

    protected $cookies = [];

    public function __construct(TestCase $test, array $cookies)
    {
        $this->test = $test;

        $this->cookies = $this->encryptCookies($cookies);
    }

    protected function encryptCookies(array $cookies)
    {
        return collect($cookies)->map('encrypt')->all();
    }

    public function __call($method, $parameters)
    {
        return $this->test->call(
            $method, $parameters[0], $parameters[1] ?? [], $this->cookies
        );
    }
}