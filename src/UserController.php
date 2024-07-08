<?php

class UserController {
    public function __construct(private UserGateway $gateway) {
    }
    public function processRequest(string $method, ?string $option): void {
        // If an ID is provided, process resource request, otherwise process collection request
        if ($id) {
            $this->processResourceRequest($method, $option);
        } else {
            $this->processCollectionRequest($method);
        };
    }
    public function processResourceRequest(string $method,int $id): void {
        $user = $this->gateway->get($id);

        if ( ! $user) {
            http_response_code(404);
            echo json_encode(["message" => "User does not exist!"]);
            return;
        }
        switch ($method) {
            case "GET":
                echo json_encode($user);
                break;
            case "PATCH":
                $postData = (array) json_decode(file_get_contents("php://input"), true);
                $errors = $this->getUserDataValidationErrors($postData);
                if ( ! empty($errors)) {
                    // return "unprocessable entity"
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                break;
            case "DELETE":
                $rows = $this->gateway->delete($id);
                echo json_encode([
                    "message" => "User $id deleted",
                    "rowsCount" => $rows
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
    }
    public function processCollectionRequest(string $method): void {
        switch ($method) {
            case "POST":
                // REGISTRATION create/register new user
                $postData = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getUserDataValidationErrors($postData);
                if ( ! empty($errors)) {
                    // return "unprocessable entity"
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                $this->register($postData);
                break;
            default:
                http_response_code(405);
                header("Allow: POST");
        }
    }
    public function register(array $postData): void {
        try {
            // check for existing user with the same email
            $existingUserCount = $this->gateway->getExistingUserCount($email);
            if ($existingUserCount > 0) {
                http_response_code(404);
                $server_response_error = array(
                    "code" => http_response_code(404),
                    "status" => false,
                    "message" => "This user is already registered."
                );
                echo json_encode($server_response_error);
            } else {
                // insert/add new user details
                // encrypt user password 
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $dataParameters = [
                    "first_name" => $postData['first_name'],
                    "last_name" => $postData['last_name'],
                    "email" => $postData['email'],
                    "password" => $password_hash
                ];
                $insertRecordFlag = $this->gateway->create($dataParameters);
                if ($insertRecordFlag > 0) {
                    $server_response_success = array(
                        "code" => http_response_code(200),
                        "status" => true,
                        "message" => "User successfully created."
                    );
                    echo json_encode($server_response_success);
                } else {
                    http_response_code(404);
                    $server_response_error = array(
                        "code" => http_response_code(404),
                        "status" => false,
                        "message" => "Failed to create user. insertRecordFlag = 0; Please try again."
                    );
                    echo json_encode($server_response_error);
                }
            }
        } catch (Exception $ex) {
            http_response_code(404);
            $server_response_error = array(
                "code" => http_response_code(404),
                "status" => false,
                "message" => "Oopps!! Something went wrong with register() method! " . $ex->getMessage()
            );
            echo json_encode($server_response_error);
        } 
    }
    public function login(string $method): string {
        try {

            if ($method === 'POST') {
    
                $postData = (array) json_decode(file_get_contents("php://input"), true);
                if (!$postData || !isset($postData['email']) || !isset($postData['password'])) {
                    http_response_code(400);
                    $server_response_error = array(
                        "code" => 400,
                        "status" => false,
                        "message" => "Invalid input."
                    );
                    return json_encode($server_response_error);
                }
                $submittedEmail = $postData['email'];
                $submittedPassword = $postData['password'];
    
                // obtain user if exists for this login and check if successful
                $fetchedUser = $this->gateway->getUserDataByEmail($submittedEmail);
                if ( ! $fetchedUser) {
                    http_response_code(404)
                    $server_response_error = array(
                        "code" => http_response_code(404),
                        "status" => false,
                        "message" => "Login failed! User not found."
                    );
                    break;
                }
                $userPasswordHash = $fetchedData['password'];
                
                if (password_verify($submittedPassword, $userPasswordHash)) {
                    // Set up JwtUtils object in order to generate token
                    $jwtUtils = new JwtUtils();
                    $jwt = $jwtUtils->generateToken($fetchedUser);
                    setcookie("jwtWawgte", $jwt, time() + (86400 * 7), "/", "", true, true);
                    http_response_code(200);
                    $server_response_success = array(
                        "code" => 200,
                        "status" => true,
                        "message" => "Login successful."
                    );
                    return json_encode($server_response_success);
                } else {
                    http_response_code(401);
                    $server_response_error = array(
                        "code" => http_response_code(401),
                        "status" => false,
                        "message" => "Authentication Failed! Password does not match!"
                    );
                    echo json_encode($server_response_error);
                    break;
                }
            }
        } catch (exception $exeption) {
            http_response_code(500);
            $server_response_error = array(
                "code" => http_response_code(401),
                "status" => false,
                "message" => "Oops! Something went wrong during login!"
            );
            echo json_encode($server_response_error); 
        }
    }
    // 
    public function getUserDataValidationErrors(array $data, bool $is_new = true): array {
        $errors = [];

        if ($is_new && (empty($data["email"]) || empty($data['password']))) {
            // If empty, add an error message to the $errors array
            $errors[] = "Email and password must not be empty";
        }
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = "Email address must be a valid one";
        }
        if (strlen($data['password']) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }
        if (!preg_match("/^(?=.*[a-zA-Z])(?=.*\d).{8,}$/", $data['password'])) {
            $errors[] = "Password must include at least one letter and one number";
        }
        return $errors;
    }
}
?>