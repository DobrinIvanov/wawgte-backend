<?php

declare(strict_types=1);
spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");
$parts = explode("/", $_SERVER["REQUEST_URI"]);


// parts[0] = ''
// parts[1] == 'backend'
// parts[2] = recipes? maybe...
// ...

if ($parts[2] != 'recipes') {
    http_response_code(404);
    exit;
}

$id = $parts[3] ?? null;

include("config/config.php");

// create database object with my details from config/config.php
$database = new Database(DB_HOST,DB_NAME, DB_USER, DB_PASS);

$gateway = new RecipeGateway($database);

$controller = new RecipeController($gateway);
$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);

?>
