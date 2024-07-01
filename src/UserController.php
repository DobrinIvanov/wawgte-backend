<?php

class UserController {
    public function __construct(private UserGateway $gateway) {

    }
    public function processRequest(string $method, ?string $id): void
    {
        // If an ID is provided, process resource request, otherwise process collection request
        if ($id) {
            $this->processResourceRequest($method, $id);
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
            case "POST":
                // login
                $formEmail = $_POST['email'];
                $formPassword = $_POST['password'];
                break;
            case "PATCH":
                // TODO on updating email only I think?
                break;
            case "DELETE":
                $rows = $this->gateway->delete($id);
                echo json_encode([
                    "message" => "User $id deleted",
                    "rows_count" => $rows
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST, PATCH, DELETE");
        }
    }
    public function processCollectionRequest(string $method): void {
        switch ($method) {
            case "POST":
                // create/register new user
                $postData = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getRegistrationValidationErrors($postData);
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
        if ( ! empty($postData['first_name']) && ! empty($postData['last_name']) && ! empty($postData['email']) && ! empty($postData['password']) ) {
            $first_name = $postData['first_name'];
            $last_name = $postData['last_name'];
            $email = $postData['email'];
            $password = $postData['password'];
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
                            "message" => "Failed to create user. Please try again."
                        );
                        echo json_encode($server_response_error);
                    }
                }
            } catch (Exception $ex) {
                http_response_code(404);
                $server_response_error = array(
                    "code" => http_response_code(404),
                    "status" => false,
                    "message" => "Oopps!! Something went wrong! " . $ex->getMessage()
                );
                echo json_encode($server_response_error);
            } // end of try/catch
        } else {
            http_response_code(404);
            $server_response_error = array(
                "code" => http_response_code(404),
                "status" => false,
                "message" => "Invalid API parameters! Please contact the administrator."
            );
            echo json_encode($server_response_error);
        }
    }
    // !!!! PLEASE TEST REGISTRATION BEFORE PROCEEDING WITH LOGIN!!!!!
    public function login($email, $password): string {
        // TODO
    }
    // PLEASE TEST VALIDATION FUNCTION
    public function getRegistrationValidationErrors(array $data, bool $is_new = true): array {
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
        // Return the array of validation errors
        return $errors;

    }
}
?>