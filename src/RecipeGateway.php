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

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // in mysql bool is represented as 0/1 and here we convert to bool true/false for each recipe
            $row["public"] = (bool) $row["public"];
            // here we append each row into a data array
            $data[] = $row;
        }
        // we return all the recipes
        return $data;

    }
}