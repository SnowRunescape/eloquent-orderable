<?php

namespace SnowRunescape\Orderable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use SnowRunescape\Orderable\Orderable;

class Comment extends Model
{
    use Orderable;

    const STATUS = [
        "inactive" => 0,
        "active" => 1,
    ];

    protected $fillable = [
        "post_id",
        "comment",
        "order",
        "status",
    ];

    protected $sortable = [
        "column_name" => "order",
        "sort_direction" => "ASC",
        "scope_columns" => [
            "post_id",
        ],
        "where_conditions" => [
            ["status", self::STATUS["active"]],
        ],
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
