<?php
// Enable strict typing for this file
declare(strict_types=1);

// Composer autoloader and dotenv loaded
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();
// load JWT
//use Firebase\JWT\JWT;

// Register an autoloader function to automatically load classes when they are used
spl_autoload_register(function ($class) {
    // Require the file containing the class based on the class name and its assumed location in the "src" directory
    require __DIR__ . "/src/$class.php";
});
// Set the custom error handler function to handle errors
set_error_handler("ErrorHandler::handleError");

// Set the custom exception handler function to handle exceptions
set_exception_handler("ErrorHandler::handleException");

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Allow specific methods and headers for CORS
    header("Content-type: application/json; charset=UTF-8");
    header('Access-Control-Allow-Origin: http://159.69.234.59:5173');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH, DELETE');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    http_response_code(204); // No Content
    exit;
}
// Set the HTTP header to indicate that the response will be in JSON format with UTF-8 encoding
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH, DELETE");
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header("Access-Control-Allow-Origin: http://159.69.234.59:5173");


// Split the REQUEST_URI into parts based on the "/" separator
$parts = explode("/", $_SERVER["REQUEST_URI"]);

// create database object with my details from dotenv file
$database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);

$object = $parts[1];
$option = $parts[2] ?? null;

switch ($object) {
    case 'recipes':
        // set up new recipe gateway connected to the database I need
        $recipeGateway = new RecipeGateway($database);
        // set up new Recipe Controller which would handle the HTTP requests and use the gateway methods I guess?
        $recipeController = new RecipeController($recipeGateway);
        // actually process the request using this "processRequest" method ( does it come from PDO?)
        $recipeController->processRequest($_SERVER["REQUEST_METHOD"], $option);
        break;
    case 'cookbooks':
        $cookbookGateway = new CookbookGateway($database);
        $cookbookController = new CookbookController($cookbookGateway);
        $cookbookController->processRequest($_SERVER["REQUEST_METHOD"], $option);
        break;
    case 'users':
        $userGateway = new UserGateway($database);
        $userController = new UserController($userGateway);
        $userController->processRequest($_SERVER["REQUEST_METHOD"], $option);
        break;
    case 'login':
        $userGateway = new UserGateway($database);
        $userController = new UserController($userGateway);
        $userController->login($_SERVER["REQUEST_METHOD"]);
        break;
    case 'search':
        if ($option === 'recipes') {
            $recipeGateway = new RecipeGateway($database);
            $recipeController = new RecipeController($recipeGateway);
            $recipeController->searchRecipes($_SERVER["REQUEST_METHOD"]);
            break;
        }
        if ($option === 'cookbooks') {
            $cookbookGateway = new CookbookGateway($database);
            $cookbookController = new CookbookController($cookbookGateway);
            $cookbookController->searchCookbooks($_SERVER["REQUEST_METHOD"]);
            break;
        }
    case 'wawgte':
        $recipeGateway = new RecipeGateway($database);
        $recipeController = new RecipeController($recipeGateway);
        // $option would be the cookbook selected
        if (!$option) {
            $recipeController->selectRandomRecipe($_SERVER["REQUEST_METHOD"]);
            break;
        }
        if ($option === 'cookbook') {
            $recipeController->selectRandomRecipePerCookbook($_SERVER["REQUEST_METHOD"]);
            break;
        }
    case 'jwt':
        $jwtUtils = new JwtUtils($_ENV['SECRET_KEY']);
        $jwToken = $_COOKIE['jwtWawgte'];
        $validationResult = $jwtUtils->validateToken($jwToken);
        if (is_array($validationResult)) {
            echo json_encode(['status' => 'success', 'data' => $validationResult]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $validationResult]);
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(["message" => "No Endpoints found!"]);
        break;
    }
?>