<?php

namespace App\Policies;

use App\User;
use App\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostAccessPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        if ($user->can('delete-published', $post)) {
            return true;
        }

        return $post->isDraft() && $user->can('delete-draft', $post);
    }
}
