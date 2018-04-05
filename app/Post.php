<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function isPublished()
    {
        return $this->status === 'published';
    }
}
