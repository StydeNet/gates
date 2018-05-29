<?php

use App\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Post::class)->times(10)->create([
            'user_id' => \App\User::where('email', 'duilio@styde.net')->first()->id,
        ]);

        factory(Post::class)->times(30)->create();
    }
}
