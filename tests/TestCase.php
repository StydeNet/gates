<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DetectRepeatedQueries;

    public function setUp(): void
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

        $this->seed('BouncerSeeder');

        $this->enableQueryLog();
    }

    public function tearDown(): void
    {
        $this->flushQueryLog();

        parent::tearDown();
    }

    protected function aUser(array $attributes = [])
    {
        return factory(User::class)->create($attributes);
    }

    protected function createAdmin()
    {
        return tap(factory(User::class)->create(), function ($user) {
            // assign role 'admin' to $user
            $user->assign('admin');
        });
    }

    protected function assertDatabaseEmpty($table, $connection = null)
    {
        $total = $this->getConnection($connection)->table($table)->count();

        $this->assertSame(0, $total, sprintf(
            "Failed asserting the table [%s] is empty. %s %s found.", $table, $total, str_plural('row', $total)
        ));
    }
}
