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

DB::schema()->create("posts", function ($table) {
    $table->increments("id");
    $table->string("title");
    $table->integer("order");
    $table->timestamps();
});
