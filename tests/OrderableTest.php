<?php

namespace SnowRunescape\Orderable\Tests;

use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit\Framework\TestCase;
use SnowRunescape\Orderable\Tests\Fixtures\Comment;
use SnowRunescape\Orderable\Tests\Fixtures\Post;
use SnowRunescape\Orderable\Tests\Fixtures\Product;

class OrderableTest extends TestCase
{
    protected function setUp(): void
    {
        DB::table("posts")->truncate();
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

    public function testOrdered()
    {
        Post::withoutEvents(function () use (&$post1, &$post2, &$post3) {
            $post1 = Post::create(["title" => "First Post", "order" => 9]);
            $post2 = Post::create(["title" => "Second Post", "order" => 5]);
            $post3 = Post::create(["title" => "Third Post", "order" => 1]);
        });

        Post::updateOrder($post1, 0);

        $this->assertEquals(0, $post1->fresh()->order);
        $this->assertEquals(1, $post3->fresh()->order);
        $this->assertEquals(2, $post2->fresh()->order);
    }

    public function testUpdateOrderWithOrderSpaced()
    {
        Post::insert([
            ["title" => "First Post", "order" => 7],
            ["title" => "Second Post", "order" => 5],
            ["title" => "Third Post", "order" => 1],
        ]);

        $posts = Post::all();

        $this->assertEquals("Third Post", $posts[0]->title);
        $this->assertEquals("Second Post", $posts[1]->title);
        $this->assertEquals("First Post", $posts[2]->title);
    }

    public function testWithoutOrdered()
    {
        Post::insert([
            ["title" => "First Post", "order" => 2],
            ["title" => "Second Post", "order" => 1],
            ["title" => "Third Post", "order" => 0],
        ]);

        $posts = Post::withoutOrdered()->get();

        $this->assertEquals("First Post", $posts[0]->title);
        $this->assertEquals("Second Post", $posts[1]->title);
        $this->assertEquals("Third Post", $posts[2]->title);
    }

    public function testPostWithCommentsOrdered()
    {
        $post = Post::create(["title" => "First Post"]);

        $post->comments()->createMany([
            ["comment" => "First Comment", "created_at" => "1997-12-13", "status" => Comment::STATUS["active"]],
            ["comment" => "Second Comment", "created_at" => "2013-12-13", "status" => Comment::STATUS["active"]],
            ["comment" => "Third Comment", "created_at" => "2024-12-13", "status" => Comment::STATUS["active"]],
        ]);

        $comments = $post->comments()->get();

        $this->assertEquals("First Comment", $comments[0]->comment);
        $this->assertEquals("Second Comment", $comments[1]->comment);
        $this->assertEquals("Third Comment", $comments[2]->comment);
    }

    public function testPostWithCommentsWithOutOrdered()
    {
        $post = Post::create(["title" => "First Post"]);

        $post->comments()->createMany([
            ["comment" => "First Comment", "created_at" => "1997-12-13", "status" => Comment::STATUS["active"]],
            ["comment" => "Second Comment", "created_at" => "2013-12-13", "status" => Comment::STATUS["active"]],
            ["comment" => "Third Comment", "created_at" => "2024-12-13", "status" => Comment::STATUS["active"]],
        ]);

        $comments = $post->comments()->withoutOrdered()->get();

        $this->assertEquals("First Comment", $comments[0]->comment);
        $this->assertEquals("Second Comment", $comments[1]->comment);
        $this->assertEquals("Third Comment", $comments[2]->comment);
    }

    public function testCommentsOrderedWithMultiplesPosts()
    {
        Comment::insert([
            ["post_id" => 1, "comment" => "First Comment on First Post", "order" => 0,"status" => Comment::STATUS["active"]],
            ["post_id" => 2, "comment" => "First Comment on Second Post", "order" => 0,"status" => Comment::STATUS["active"]],
            ["post_id" => 1, "comment" => "Second Comment on First Post", "order" => 1,"status" => Comment::STATUS["active"]],
            ["post_id" => 2, "comment" => "Second Comment on Second Post", "order" => 1,"status" => Comment::STATUS["inactive"]],
            ["post_id" => 1, "comment" => "Third Comment on First Post", "order" => 2,"status" => Comment::STATUS["active"]],
            ["post_id" => 2, "comment" => "Third Comment on Second Post", "order" => 1,"status" => Comment::STATUS["active"]],
        ]);

        $comment = Comment::create(["post_id" => 2, "comment" => "Four Comment on Second Post", "status" => Comment::STATUS["active"]]);

        $this->assertEquals(2, $comment->fresh()->order);
    }

    public function testSortableWhenCreatingFalse()
    {
        $product = Product::create([
            "title" => "Title of First Product",
            "description" => "Description of First Product",
        ]);

        $this->assertEquals("Title of First Product", $product->title);
        $this->assertEquals("Description of First Product", $product->description);
        $this->assertNull($product->order);
    }
}
