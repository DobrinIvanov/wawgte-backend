<?php

class CookbookController {
    public function __construct(private CookbookGateway $gateway) {
    }
    // Method to process incoming requests
    public function processRequest(string $method, ?string $id): void {
        // If an ID is provided, process single resource request, otherwise process collection request
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        };
    }
    public function processResourceRequest(string $method, ?string $id): void {
        $cookbook = $this->gateway->get($id);

        if ( ! $cookbook) {
            http_response_code(404);
            echo json_encode(["message" => "Cookbook not found!"]);
            return;
        }
        switch ($method) {
            case "GET":
                echo json_encode($cookbook);
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
                $rows_count = $this->gateway->update($product, $data);

                // for successful post request that adds content to db, its best to return 201 instead of 200
                http_response_code(200);

                // Return a JSON response indicating successful creation of the recipe
                echo json_encode([
                    "message" => "Cookbook edited!",
                    "Affected Rows" => $rows_count
                ]);
                break; 
            case "DELETE":
                $rows = $this->gateway->delete($id);

                echo json_encode([
                    "message" => "Cookbook $id deleted",
                    "rows_count" => $rows
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
    }
    public function processCollectionRequest(string $method): void {
        $cookbooksData = $this->gateway->getAll();

        switch ($method) {
            case "GET":
                echo json_encode($cookbooksData);
                break;
            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);
                
                $errors = $this->getCookbookValidationErrors($data);

                if ( ! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;

                }
                $id = $this->gateway->create($data);
                http_response_code(201);

                echo json_encode([
                    "message" => "Cookbook added!",
                    "id" => $id
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }
    private function getCookbookValidationErrors(array $data, bool $is_new = true): array {
        $errors = [];
        if ($is_new && empty($data["title"])) {
            $errors[] = "cookbook title is required";
        }
        // check if title(title key) is empty
        if ($is_new && empty($data["description"])) {
            $errors[] = "description should not be empty";
        }
        if (array_key_exists("user_id", $data)) {
                // If it exists, validate  it's an integer using FILTER_VALIDATE_INT
            if (filter_var($data["user_id"], FILTER_VALIDATE_INT) === false){
                // If not an integer, add an error message to the $errors array
                $errors[] = "user_id must be an integer";
            }
        }
        // Return the array of validation errors
        return $errors;
    }
}
