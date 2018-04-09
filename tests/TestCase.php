<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createUser(array $attributes = [])
    {
        return factory(User::class)->create($attributes);
    }

    protected function createAdmin()
    {
        return factory(User::class)->states('admin')->create();
    }
}
