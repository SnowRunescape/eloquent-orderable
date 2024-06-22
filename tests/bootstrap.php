<?php

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Events\Dispatcher;

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
$db->setEventDispatcher(new Dispatcher());
$db->bootEloquent();

DB::schema()->dropIfExists("posts");
DB::schema()->dropIfExists("comments");
DB::schema()->dropIfExists("products");

DB::schema()->create("posts", function ($table) {
    $table->increments("id");
    $table->string("title");
    $table->integer("order")->default(0);
    $table->timestamps();
});

DB::schema()->create("comments", function ($table) {
    $table->increments("id");
    $table->integer("post_id");
    $table->string("comment");
    $table->timestamps();
    $table->integer("order")->default(0);
    $table->integer("status");
});

DB::schema()->create("products", function ($table) {
    $table->increments("id");
    $table->string("title");
    $table->string("description");
    $table->integer("order")->default(0);
    $table->timestamps();
});
