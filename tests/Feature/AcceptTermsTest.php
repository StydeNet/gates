<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcceptTermsTest extends TestCase
{
    /** @test */
    function a_user_can_accept_the_terms_and_conditions()
    {
        $this->withoutExceptionHandling();

        $this->post('accept-terms')
            ->assertRedirect('/')
            ->assertCookieMissing('accept_terms');

        $this->post('accept-terms', ['accept' => '1'])
            ->assertRedirect('/')
            ->assertCookie('accept_terms', '1');
    }
}
