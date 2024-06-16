<?php

namespace SnowRunescape\Orderable\Tests;

use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit\Framework\TestCase;
use SnowRunescape\Orderable\Tests\Fixtures\Post;

class OrderableTest extends TestCase
{
    protected function setUp(): void
    {
        DB::table('posts')->truncate();
    }

    public function testOrderableTraitAddsOrderColumn()
    {
        $this->assertTrue(DB::schema()->hasColumn("posts", "order"));
    }

    public function testNewModelHasInitialOrder()
    {
        $post1 = Post::create(["title" => "First Post"]);
        $post2 = Post::create(["title" => "Second Post"]);

        $this->assertEquals(0, $post1->order);
        $this->assertEquals(1, $post2->order);
    }

    public function testUpdateOrder()
    {
        $post1 = Post::create(["title" => "First Post"]);
        $post2 = Post::create(["title" => "Second Post"]);
        $post3 = Post::create(["title" => "Third Post"]);

        Post::updateOrder($post2, 2);

        $this->assertEquals(2, $post2->fresh()->order);
        $this->assertEquals(0, $post1->fresh()->order);
        $this->assertEquals(1, $post3->fresh()->order);
    }
}
