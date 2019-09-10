<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createAdmin();

        $this->createAuthor();

        $this->createUser();
    }

    protected function createAdmin()
    {
        $admin = factory(\App\User::class)->create([
            'email' => 'admin@styde.net',
            'name' => 'Administrator',
        ]);

        $admin->assign('admin');
    }

    protected function createAuthor()
    {
        $user = factory(\App\User::class)->create([
            'name' => 'Duilio Palacios',
            'email' => 'duilio@styde.net',
        ]);

        $user->assign('author');
    }

    protected function createUser()
    {
        $user = factory(\App\User::class)->create([
            'name' => 'Duilio Palacios',
            'email' => 'user@example.com',
        ]);
    }
}
