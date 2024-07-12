<?php

// recipe controller, processes requests for recipes and uses methods from the gateway
class RecipeController {
    // Constructor accepting a RecipeGateway instance
    public function __construct(private RecipeGateway $gateway) {
    }
    // Method to process incoming requests
    public function processRequest(string $method, ?string $id): void {
        // If an ID is provided, process resource request, otherwise process collection request
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        };
    }
    // Method to process resource request (e.g., request for a specific recipe)
    private function processResourceRequest(string $method, string $id): void {
        // TODO: Resource request processing goes here
        $recipe = $this->gateway->get($id);

        if ( ! $recipe) {
            http_response_code(404);
            echo json_encode(["message" => "Product not found!"]);
            return;
        }
        switch ($method) {
            case "GET":
                echo json_encode($recipe);
                break;
            case "PATCH":
                // because our request works with JSON, we need to use file_get_contents instead of $_POST
                // also we need to use array format, so we convert that to array with json_decode( _ ,true)
                // we also use (array) so that empty request returns an array instead of NULL
                $data = (array) json_decode(file_get_contents("php://input"), true);
                
                // validate data and get errors if any, we use "false" because that should be "true" for adding new records only
                $errors = $this->getValidationErrors($data, false);

                if ( ! empty($errors)) {
                    // return "unprocessable entity"
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;

                }
                // here $product > $current and $data > $new data
                $rowsCount = $this->gateway->updateRecipe($product, $data);

                // for successful post request that adds content to db, its best to return 201 instead of 200
                http_response_code(200);

                // Return a JSON response indicating successful creation of the recipe
                echo json_encode([
                    "message" => "Recipe edited!",
                    "Affected Rows" => $rowsCount
                ]);
                break; 
            case "DELETE":
                $rows = $this->gateway->delete($id);

                echo json_encode([
                    "message" => "Product $id deleted",
                    "rows_count" => $rows
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
                break;
        }

    }
    private function processCollectionRequest(string $method): void {
        // Switch based on the HTTP method of the request
        switch ($method) {
            case "GET":
                // Return JSON-encoded array of all recipes from the gateway
                echo json_encode($this->gateway->getAll());
                break;
                // return json_encode($this->gateway->getAll());
            case "POST":
                // because our request works with JSON, we need to use file_get_contents instead of $_POST
                // also we need to use array format, so we convert that to array with json_decode( _ ,true)
                // we also use (array) so that empty request returns an array instead of NULL
                $data = (array) json_decode(file_get_contents("php://input"), true);
                // validate data and get errors if any
                $errors = $this->getRecipeValidationErrors($data, true);

                if ( ! empty($errors)) {
                    // return "unprocessable entity"
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                // Create a new recipe using data from the request and get the ID
                $id = $this->gateway->create($data);

                // for successful post request that adds content to db, its best to return 201 instead of 200
                http_response_code(201);

                // Return a JSON response indicating successful creation of the recipe
                echo json_encode([
                    "message" => "Recipe added!",
                    "id" => $id
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
                break;
        }
    }
    private function getRecipeValidationErrors(array $data, bool $is_new = true): array {
        $errors = [];
        // Check if the "title" key in the data array is empty
        if ($is_new && empty($data["title"])) {
            // If empty, add an error message to the $errors array
            $errors[] = "recipe title is required";
        }
        // Check if the "user_id" key exists in the data array
        if (array_key_exists("user_id", $data)) {
                // If it exists, validate if it's an integer using FILTER_VALIDATE_INT
            if (filter_var($data["user_id"], FILTER_VALIDATE_INT) === false){
                // If not an integer, add an error message to the $errors array
                $errors[] = "user_id must be an integer";
            }
        }
        // Return the array of validation errors
        return $errors;
    }
    // public function searchRecipes(string $method): array | false {
    //     if ($method === 'POST') {
    //         $data = (array) json_decode(file_get_contents("php://input"), true);
    //         if ( empty($data) || empty($data['searchString'])) {
    //             http_response_code(400);
    //             $server_response_error = array(
    //                 "code" => 400,
    //                 "status" => false,
    //                 "message" => "Invalid input. Empty search string!"
    //             );
    //             echo json_encode($server_response_error);
    //             return false;
    //         }
    //         $searchTerm = $data["searchString"];
    //         $recipesFound = $this->gateway->search($searchTerm);
    //         return $recipesFound;
    //     } else {
    //         http_response_code(405);
    //     }
    // }
    public function searchRecipes($method): void {
        // Check if the request method is GET
        if ($method !== 'GET') {
            http_response_code(405); // Method Not Allowed
            return;
        }
        // Check if the search term is present in the query string
        if (!isset($_GET['searchString']) || empty($_GET['searchString'])) {
            http_response_code(400); // Bad Request
            $server_response_error = [
            "code" => 400,
            "status" => false,
            "message" => "Invalid input. Empty search string or not set at all!",
            ];
            echo json_encode($server_response_error);
            return;
        }
        // Extract search term from query string
        $searchTerm = $_GET['searchString'];
        // Call the gateway to search recipes
        echo json_encode($this->gateway->search($searchTerm));
        return;
    }

    public function selectRandomRecipe(): void {
        $ids = $this->gateway->getAllIds();
        $randomKey = array_rand($ids);
        $randomId = $ids[$randomKey];
        echo json_encode($this->gateway->get($randomId));
    }

    public function selectRandomRecipePerCookbook($cookbookId): void {
        
    }
    public function selectRandomRecipeInFavourites($userId): void {
        
    }
}