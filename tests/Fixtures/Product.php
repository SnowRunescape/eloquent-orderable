<?php

namespace SnowRunescape\Orderable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use SnowRunescape\Orderable\Orderable;

class Product extends Model
{
    use Orderable;

    protected $fillable = [
        "title",
        "description",
        "order",
    ];

    protected $sortable = [
        "sort_when_creating" => false,
    ];
}
