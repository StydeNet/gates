<?php

namespace Tests;

use Bouncer;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DetectRepeatedQueries;

    public function setUp()
    {
        parent::setUp();

        TestResponse::macro('viewData', function ($key) {
            $this->ensureResponseHasView();

            $this->assertViewHas($key);

            return $this->original->$key;
        });

        TestResponse::macro('assertViewCollection', function ($var) {
            return new TestCollectionData($this->viewData($var));
        });

        $this->enableQueryLog();
    }

    public function tearDown()
    {
        $this->flushQueryLog();

        parent::tearDown();
    }

    protected function createUser(array $attributes = [])
    {
        return factory(User::class)->create($attributes);
    }

    protected function createAdmin()
    {
        $user = factory(User::class)->create();

        $user->allow()->everything();

        // assign role 'admin' to $user
        //Bouncer::assign('admin')->to($user);

        // allow the role 'admin' to have access to everything
        //Bouncer::allow('admin')->everything();

        return $user;
    }

    protected function assertDatabaseEmpty($table, $connection = null)
    {
        $total = $this->getConnection($connection)->table($table)->count();

        $this->assertSame(0, $total, sprintf(
            "Failed asserting the table [%s] is empty. %s %s found.", $table, $total, str_plural('row', $total)
        ));
    }
}
