<?php

class UserGateway {
    private PDO $conn;

    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    public function get(int $id): array | false {
        
        $sql = "select user_id,username,email from users where user_id=:user_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":user_id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $fetched_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $fetched_user;
    }

    public function updateEmail(array $currentEmail, array $newEmail): int {
        $sql = "UPDATE users
        SET email = :newEmail
        WHERE email = :currentEmail;";

        $stmtUpdateEmail = $this->conn->prepare($sql);
        $stmtUpdateEmail->bindValue(":newEmail", $newEmail, PDO::PARAM_STR);
        $stmtUpdateEmail->bindValue(":currentEmail", $currentEmail, PDO::PARAM_STR);

        $stmtUpdateEmail->execute();

        return $stmtUpdateEmail->rowCount();
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

    public function create(array $userData) {

        $sql = "INSERT INTO users (username, email, password)
                VALUES (:username, :email, :hashed_password)";

        $stmtCreateUser = $this->conn->prepare($sql);
        $stmtCreateUser->bindValue(":username", $username, PDO::PARAM_STR);
        $stmtCreateUser->execute();
        
        return $this->conn->lastInsertId();
    }
    public function delete(string $id): int {
        $sql = "DELETE FROM users WHERE user_id=:user_id";

        $stmtDeleteUser = $this->conn->prepare($sql);
        $stmtDeleteUser->bindValue(":id", $id, PDO::PARAM_INT);
        $stmtDeleteUser->execute();

        return $stmtDeleteUser->rowCount();

    }
    // public function getAll(){
    // }
    // not sure if that ^ would be required 
}

?>