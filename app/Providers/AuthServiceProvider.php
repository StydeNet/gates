<?php

namespace App\Providers;

use App\{Post, User};
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function (User $user) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        Gate::define('update-post', function (User $user, Post $post) {
            return $user->owns($post);
        });

        Gate::define('delete-post', function (User $user, Post $post) {
            return $user->owns($post) && !$post->isPublished();
        });
    }
}
