<?php

declare(strict_types=1);
spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");
$parts = explode("/", $_SERVER["REQUEST_URI"]);

// switch case?
// parts[0] = ''
// parts[1] == 'recipes'
// parts[2] = id ? maybe...
// ...

if ($parts[1] != 'recipes') {
    http_response_code(404);
    exit;
}

$id = $parts[2] ?? null;

include("config/config.php");

// create database object with my details from config/config.php
$database = new Database(DB_HOST,DB_NAME, DB_USER, DB_PASS);

// set up new recipe gateway connected to the database I need
$gateway = new RecipeGateway($database);

// set up new Recipe Controller which would handle the HTTP requests and use the gateway methods I guess?
$controller = new RecipeController($gateway);

// actually process the request using this "processRequest" method ( does it come from PDO?)
$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);

?>
