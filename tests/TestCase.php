<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DetectRepeatedQueries;

    public function setUp()
    {
        parent::setUp();

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
        return factory(User::class)->states('admin')->create();
    }

    protected function assertDatabaseEmpty($table, $connection = null)
    {
        $total = $this->getConnection($connection)->table($table)->count();

        $this->assertSame(0, $total, sprintf(
            "Failed asserting the table [%s] is empty. %s %s found.", $table, $total, str_plural('row', $total)
        ));
    }
}
