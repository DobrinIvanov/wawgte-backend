<?php
class RecipeGateway {  
    // Declare a private property to hold the PDO connection 
    private PDO $conn;

    // Constructor method to initialize the RecipeGateway object with a database connection
    public function __construct(Database $database)
    {   
        // Get the PDO connection from the injected Database object
        $this->conn = $database->getConnection();
    }

    public function getAll(): array
    {
        // sql code to select all recipes from the table
        $sql = "SELECT *
                FROM recipes;";

        // set statement to current conn ($database.getConnection) > execute query(PDO stuff) method of it
        $stmt = $this->conn->query($sql);

        // create array to store all recipes that we will return
        $data = [];

        //
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // in mysql bool is represented as 0/1 and here we convert to bool true/false for each recipe
            $row["public"] = (bool) $row["public"];
            // here we append each row into a data array
            $data[] = $row;
        }
        // we return all the recipes
        return $data;

    }

    // create method on the RecipeGateway that would handle post requests towards backend/recipes and create recipes in the db
    public function create(array $data): string {

        // SQL query to insert recipe data into the 'recipes' table
        $sql = "INSERT INTO recipes (title, description, instructions, public)
                VALUES (:title, :description, :instructions, :public)";

        // Prepare the SQL statement for execution
        $stmt = $this->conn->prepare($sql);

        // Bind values to the placeholders in the SQL query
        $stmt->bindValue(":title", $data["title"], PDO::PARAM_STR);
        $stmt->bindValue(":description", $data["description"], PDO::PARAM_STR);
        $stmt->bindValue(":instructions", $data["instructions"], PDO::PARAM_STR);
        $stmt->bindValue(":public", ((bool) $data["public"] ?? false), PDO::PARAM_BOOL);

        $stmt->execute();

        // Return the last inserted ID of the newly created recipe
        return $this->conn->lastInsertId();

    }
    public function get(string $id): array | false
    {
        $sql = "SELECT *
                FROM recipes
                WHERE recipe_id = :id;";
        
        // Prepare the SQL statement for execution
        $stmt = $this->conn->prepare($sql);
        // Bind the provided ID parameter to the prepared statement, specifying it as an integer
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        // Execute the prepared statement
        $stmt->execute();
        // Fetch the result set as an associative array
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data !== false) {
            $data["public"] = (bool) $data["public"];
        }

        // Return the fetched data
        return $data;
    }
    public function update(array $current, array $new): int {

        $sql = "UPDATE recipes
                SET title = :title ,public = :public
                WHERE recipe_id = :recipe_id;";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":title", $new["title"] ?? $current["title"], PDO::PARAM_STR);
        $stmt->bindValue(":public", $new["public"] ?? $current["public"], PDO::PARAM_BOOL);
        $stmt->bindValue(":recipe_id", $current["recipe_id"], PDO::PARAM_INT);

        $stmt->execute();

        // return the number of rows that were affected by the SQL statement
        return $stmt->rowCount();
    }
    public function delete(string $id): int {
        $sql = "DELETE FROM recipes
                WHERE recipe_id = :recipe_id;";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":recipe_id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
    
}