<?php
class RecipeGateway
{   
    private PDO $conn;

    public function __construct(Database $database)
    {
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
        $sql = "INSERT INTO recipes (title, description, instructions, user_id, public)
                VALUES (:title, :description, :instructions, :user_id, :public)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":title", $data["title"], PDO::PARAM_STR);
        $stmt->bindValue(":description", $data["description"], PDO::PARAM_STR);
        $stmt->bindValue(":instructions", $data["instructions"], PDO::PARAM_STR);
        $stmt->bindValue(":user_id", $data["user_id"], PDO::PARAM_INT);
        $stmt->bindValue(":public", ((bool) $data["public"] ?? false), PDO::PARAM_BOOL);

        $stmt->execute();

        return $this->conn->lastInsertId();

    }
}