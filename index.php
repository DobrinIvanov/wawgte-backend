<?php

declare(strict_types=1);
spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");
$parts = explode("/", $_SERVER["REQUEST_URI"]);

if ($parts[1] != 'recipes') {
    http_response_code(404);
    exit;
}

$id = $parts[2] ?? null;

include("config/config.php");
$database = new Database(DB_HOST,DB_NAME, DB_USER, "asdaDS");

$database->getConnection();

$controller = new RecipeController;
$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);

?>