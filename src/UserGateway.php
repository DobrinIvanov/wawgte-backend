<?php

class UserGateway {
    private PDO $conn;

    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }
    public function get($id): array | false {
        
        $sql = "select user_id,username,email from users where user_id=:user_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":user_id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $fetched_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $fetched_user;
    }
    public function update(){
        $sql = ""
    }
    public function create(array $userData) {

        $sql = "INSERT INTO users (username, email, password)
                VALUES (:username, :email, :hashed_password)";

        $stmtCreateUser = $this->conn->prepare($sql);
        $stmtCreateUser->bindValue(":username", $username, PDO::PARAM_STR);
        $stmtCreateUser->execute();
        
        return $this->conn->lastInsertId();
    }
    public function delete(){
        $sql = ""
    }
    // public function getAll(){
    // }
    // not sure if that ^ would be required 
}

?>