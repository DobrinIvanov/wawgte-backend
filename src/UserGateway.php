<?php

class UserGateway {
    private PDO $conn;

    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }
    public function get() {
        $sql = "select user_id,username,email from users where user_id=:user_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":user_id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $fetched_user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fetched_user; 
    }
    public function update(){
        
    }
    public function create(){
        
    }
    public function delete(){
        
    }
    // public function getAll(){
    // }
    // not sure if that ^ would be required 
}

?>