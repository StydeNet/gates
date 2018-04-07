<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title'];
    //protected $guarded = [];

    public function isPublished()
    {
        return $this->status === 'published';
    }
}
