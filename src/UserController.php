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
                $user = $this->gateway->get($id);
                if ( ! $user) {
                    http_response_code(404);
                    echo json_encode(["message" => "User does not exist!"]);
                    return;
                }
                break;
            case "PATCH":
                break;
            case "DELETE":
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST, PATCH, DELETE");
        }
    }
    public function processCollectionRequest(string $method): void {
        switch ($method) {
            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST, PATCH, DELETE");
        }
    }
    public function register(array $postData): void {
        if ( ! empty($_POST['username']) && ! empty($_POST['email']) && ! empty($_POST['password']) )
        {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            try {
                // check for existing user with the same username or email
                $sql_check_existing = "select * from users where username=:username";
                $stmt_check_existing_user = $this->conn->prepare($sql_check_existing);
                $stmt_check_existing_user->bindValue(":username", $username);
                $stmt_check_existing_user->execute();
                $existing_user_count = $stmt_check_existing_user->rowCount();
                if ($existing_user_count > 0) {
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
                        "username" => $_POST['username'],
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
        }
    }
    // !!!! PLEASE TEST REGISTRATION BEFORE PROCEEDING WITH LOGIN!!!!!
    public function login($username, $password): string {

    }

}

?>