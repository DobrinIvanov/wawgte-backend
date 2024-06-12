<?php
class CookbookGateway
{  
    // Declare a private property to hold the PDO connection 
    private PDO $conn;

    // Constructor method to initialize the RecipeGateway object with a database connection
    public function __construct(Database $database)
    {   
        // Get the PDO connection from the injected Database object
        $this->conn = $database->getConnection();
    }