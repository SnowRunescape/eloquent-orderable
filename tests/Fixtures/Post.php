<?php

namespace SnowRunescape\Orderable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use SnowRunescape\Orderable\Orderable;

class Post extends Model
{
    use Orderable;

    protected $fillable = [
        "order",
        "title",
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
