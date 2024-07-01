<?php

class UserGateway {
    private PDO $conn;

    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    public function get(int $id): array | false {
        
        $sql = "SELECT user_id,first_name,last_name,email FROM users WHERE user_id=:user_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":user_id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $fetched_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $fetched_user;
    }

    public function updateEmail(array $current, array $new): int {

        $sql = "UPDATE users SET email = :email WHERE user_id = :user_id;";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":title", $new["email"], PDO::PARAM_STR);
        $stmt->bindValue(":user_id", $current["user_id"], PDO::PARAM_INT);

        $stmt->execute();

        // return the number of rows that were affected by the SQL statement
        return $stmt->rowCount();
    }

    public function changePassword(int $user_id, string $newPassword): int {
        $sql = "UPDATE users
        SET password = :newPassword
        WHERE user_id = :user_id;";

        $stmtUpdateEmail = $this->conn->prepare($sql);
        $stmtUpdateEmail->bindValue(":newPassword", $newPassword, PDO::PARAM_STR);
        $stmtUpdateEmail->bindValue(":user_id", $user_id, PDO::PARAM_STR);

        $stmtUpdateEmail->execute();

        return $stmtUpdateEmail->rowCount(); 
    }

    public function create(array $userData): int {

        $sql = "INSERT INTO users (first_name, last_name, email, password)
                VALUES (:first_name, :last_name, :email, :hashed_password)";

        $stmtCreateUser = $this->conn->prepare($sql);
        $stmtCreateUser->bindValue(":first_name", $userData['first_name'], PDO::PARAM_STR);
        $stmtCreateUser->bindValue(":last_name", $userData['last_name'], PDO::PARAM_STR);
        $stmtCreateUser->bindValue(":email", $userData['email'], PDO::PARAM_STR);
        $stmtCreateUser->bindValue(":hashed_password", $userData['password'], PDO::PARAM_STR);

        $stmtCreateUser->execute();
        
        return $this->conn->lastInsertId();
    }
    public function delete(string $id): int {
        $sql = "DELETE FROM users WHERE user_id=:user_id";

        $stmtDeleteUser = $this->conn->prepare($sql);
        $stmtDeleteUser->bindValue(":user_id", $id, PDO::PARAM_INT);
        $stmtDeleteUser->execute();

        return $stmtDeleteUser->rowCount();

    }
    public function getExistingUserCount(string $email): bool {
        $sql = "SELECT * FROM users WHERE email=:email";

        $stmtCheckExistingUser = $this->conn->prepare($sql);
        $stmtCheckExistingUser->bindValue(":email", $email);
        $stmtCheckExistingUser->execute();

        $existingUserCount = $stmtCheckExistingUser->rowCount();
        
        return $existingUserCount;
    }
    // public function getAll(){
    // }
    // not sure if that ^ would be required 
}

?>