<?php

namespace App\Providers;

use App\{
    Policies\OldPostPolicy, Post, User
};
use Illuminate\Support\Facades\Cookie;
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
        //'App\Model' => 'App\Policies\ModelPolicy',
        //'App\Post' => 'App\Policies\PostPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('see-content', function (?User $user) {
            return $user || Cookie::get('accept_terms') === '1';
        });

        Gate::guessPolicyNamesUsing(function ($class) {
            $classDirname = str_replace('/', '\\', dirname(str_replace('\\', '/', $class)));

            return [$classDirname.'\\Policies\\'.class_basename($class).'AccessPolicy'];
        });
    }
}
