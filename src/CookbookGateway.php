<?php
class CookbookGateway {  
    // Declare a private property to hold the PDO connection 
    private PDO $conn;

    // Constructor method to initialize the RecipeGateway object with a database connection
    public function __construct(Database $database)
    {   
        // Get the PDO connection from the injected Database object
        $this->conn = $database->getConnection();
    }

    public function get(string $id): array | false {
        $sql = "SELECT *
                FROM cookbooks
                WHERE id =:id;";
        
        $stmt = $this->$con->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data !== false) {
            $data["public"] = (bool) $data["public"];
        }
        return $data;
    }

    public function getAll(): array | false {
        $sql = "SELECT *
                FROM cookbooks;";
        
        $stmt = $this->$con->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASOC);

        if ($data !== false) {
            $data["public"] = (bool) $data["public"];
        }

        return $data;
    }
}