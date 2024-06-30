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
                header("Allow: GET, PATCH, DELETE");
        }
    }
    public function processCollectionRequest(string $method): void {
        switch ($method) {
            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);
                $this->register($data);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST, PATCH, DELETE");
        }
    }
    public function register(array $postData): void {
        if ( ! empty($_POST['first_name']) && ! empty($_POST['last_name']) && ! empty($_POST['email']) && ! empty($_POST['password']) ) {
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            try {
                // check for existing user with the same username or email
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
                        "first_name" => $_POST['first_name'],
                        "last_name" => $_POST['last_name'],
                        "email" => $_POST['email'],
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
                    "message" => "Oopps!! Something Went Wrong! " . $ex->getMessage()
                );
                echo json_encode($server_response_error);
            } // end of try/catch
        } else {
            http_response_code(404);
            $server_response_error = array(
                "code" => http_response_code(404),
                "status" => false,
                "message" => "Invalid API parameters! Please contact the administrator or refer to the documentation for assistance."
            );
            echo json_encode($server_response_error);
        }
    }
    // !!!! PLEASE TEST REGISTRATION BEFORE PROCEEDING WITH LOGIN!!!!!
    // public function login($username, $password): string {
    //     // TODO
}
    // }
?>