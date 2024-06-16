<?php

namespace SnowRunescape\Orderable\Tests;

use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit\Framework\TestCase;
use Illuminate\Events\Dispatcher;
use SnowRunescape\Orderable\Tests\Fixtures\Post;

class OrderableTest extends TestCase
{
    protected function setUp(): void
    {
        $db = new DB;
        $db->addConnection([
            "driver" => $_ENV["DB_DRIVE"],
            "host" => $_ENV["DB_HOST"],
            "database" => $_ENV["DB_DATABASE"],
            "username" => $_ENV["DB_USERNAME"],
            "password" => $_ENV["DB_PASSWORD"],
            "charset" => "utf8mb4",
            "collation" => "utf8mb4_unicode_ci",
        ]);
        $db->setAsGlobal();
        $db->setEventDispatcher(new Dispatcher);
        $db->bootEloquent();

        DB::schema()->create("posts", function ($table) {
            $table->increments("id");
            $table->string("title");
            $table->integer("order")->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        // Limpa a tabela de teste após cada teste
        DB::schema()->drop("posts");
    }

    public function testOrderableTraitAddsOrderColumn()
    {
        $this->assertTrue(DB::schema()->hasColumn("posts", "order"));
    }

    public function testNewModelHasInitialOrder()
    {
        $post1 = Post::create(["title" => "First Post"]);
        $this->assertEquals(1, $post1->order);
    }

    public function testUpdateOrder()
    {
        $post1 = Post::create(["title" => "First1 Post", "order" => 1]);
        $post2 = Post::create(["title" => "Second Post", "order" => 2]);
        $post3 = Post::create(["title" => "Third Post", "order" => 3]);

        Post::updateOrder($post2, 3);

        $this->assertEquals(3, $post2->fresh()->order);
        $this->assertEquals(1, $post1->fresh()->order);
        $this->assertEquals(2, $post3->fresh()->order);
    }
}
