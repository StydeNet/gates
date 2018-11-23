<?php

namespace Tests\Feature;

use App\Http\Middleware\EncryptCookies;
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

        $this->withoutExceptionHandling();

        $this->post = factory(Post::class)->create([
            'teaser' => 'The teaser',
            'content' => 'The content of the post',
        ]);
    }

    /** @test */
    function logged_in_users_can_see_the_content_of_the_posts()
    {
        $this->actingAs($this->aUser());

        $response = $this->get($this->postPage());

        $response->assertOk()
            ->assertViewIs('posts.show')
            ->assertSee('The teaser')
            ->assertSee('The content of the post');
    }
    
    /** @test */
    function anonymous_users_cannot_see_the_content_without_accepting_the_terms()
    {
        $this->get($this->postPage())
            ->assertStatus(200)
            ->assertSee('The teaser')
            ->assertDontSee('The content of the post');
    }

    /** @test */
    function anonymous_users_can_see_the_content_if_they_have_accepted_the_terms()
    {
        $this->withCookies(['accept_terms' => '1'])
            ->get($this->postPage())
            ->assertOk()
            ->assertSee('The content of the post');
    }

    protected function postPage()
    {
        return "posts/{$this->post->id}";
    }

    protected function withTermsAccepted()
    {
        return ['accept_terms' => encrypt('1', false)];
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
        return array_map(function ($value) {
            return encrypt($value, false);
        }, $cookies);

//        return collect($cookies)->map(function ($value) {
//            return encrypt($value, false);
//        })->all();
    }

    public function __call($method, $parameters)
    {
        return $this->test->call(
            $method, $parameters[0], $parameters[1] ?? [], $this->cookies
        );
    }
}