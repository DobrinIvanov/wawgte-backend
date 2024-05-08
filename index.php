<?php
// Enable strict typing for this file
declare(strict_types=1);

// Register an autoloader function to automatically load classes when they are used
spl_autoload_register(function ($class) {
    // Require the file containing the class based on the class name and its assumed location in the "src" directory
    require __DIR__ . "/src/$class.php";
});
// Set the custom error handler function to handle errors
set_error_handler("ErrorHandler::handleError");

// Set the custom exception handler function to handle exceptions
set_exception_handler("ErrorHandler::handleException");

// Set the HTTP header to indicate that the response will be in JSON format with UTF-8 encoding
header("Content-type: application/json; charset=UTF-8");

// Split the REQUEST_URI into parts based on the "/" separator
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
