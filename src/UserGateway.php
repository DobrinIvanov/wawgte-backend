<?php

class UserGateway {
    private PDO $conn;

    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }
    public function get() {
        
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