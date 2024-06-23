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
                WHERE cookbook_id=:id;";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data !== false) {
            $data["public"] = (bool) $data["public"];
        }
        return $data;
    }

    public function getAll(): array {
        $sql = "SELECT *
                FROM cookbooks;";
        
        $stmt = $this->conn->query($sql);
        $data = [];
        // $data = $stmt->fetch(PDO::FETCH_ASSOC);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // in mysql bool is represented as 0/1 and here we convert to bool true/false for each recipe
            $row["public"] = (bool) $row["public"];
            // here we append each row into a data array
            $data[] = $row;
        }
        // we return all the recipes
        return $data;
    }
    public function delete(int $id): int {
        $sql = "DELETE FROM cookbooks WHERE cookbook_id=:cookbook_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":cookbook_id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function update(array $current, array $new): int {

        $sql = "UPDATE cookbooks
                SET title = :title ,public = :public
                WHERE cookbook_id = :cookbook_id;";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":title", $new["title"] ?? $current["title"], PDO::PARAM_STR);
        $stmt->bindValue(":public", $new["public"] ?? $current["public"], PDO::PARAM_BOOL);
        $stmt->bindValue(":cookbook_id", $current["cookbook_id"], PDO::PARAM_INT);

        $stmt->execute();

        // return the number of rows that were affected by the SQL statement
        return $stmt->rowCount();
    }
    // TODO 
    public function create(array $data): string {
        $sql = "INSERT INTO cookbooks (title, description, user_id, public) 
                VALUES ( :title, :description, :user_id, :public)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":title", $data["title"], PDO::PARAM_STR);
        $stmt->bindValue(":description", $data["description"], PDO::PARAM_STR);
        $stmt->bindValue(":user_id", $data["user_id"], PDO::PARAM_INT);
        $stmt->bindValue("public", $data["public"], PDO::PARAM_BOOL);
        
        $stmt->execute();
        
        return $this->conn->lastInsertId();

    }
}