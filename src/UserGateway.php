<?php

class UserGateway {
    private PDO $conn;

    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }
    public function get() {
        
    }
    public function getAll(){
    }
    public function update(){
        
    }
    public function create(){
        
    }
    public function delete(){
         
    }
    // leaving as reminder, not sure that one would be required at all
}

?>