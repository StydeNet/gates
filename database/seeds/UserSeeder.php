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

        $user = factory(\App\User::class)->create([
            'email' => 'duilio@styde.net',
        ]);

        $user->assign('author');
    }

    protected function createAdmin()
    {
        $admin = factory(\App\User::class)->create([
            'email' => 'admin@styde.net',
            'name' => 'Administrator',
        ]);

        $admin->assign('admin');
    }
}
