<?php
class Database
{
    // Constructor method to initialize the Database object with connection details
    public function __construct(private string $host, private string $name,
                                private string $user, private string $password) {
    }
    // Method to establish a database connection and return a PDO object
    public function getConnection(): PDO {
        // Construct the DSN (Data Source Name) string for connecting to the database
        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";
        // Create a new PDO (PHP Data Objects) instance with the specified connection details
        // PDO is a PHP extension for interacting with databases in a consistent manner
        // The connection is established using the DSN, username, password, and options array
        $pdo = new PDO($dsn, $this->user, $this->password, [
            // Disable emulation of prepared statements (let the database handle them)
            PDO::ATTR_EMULATE_PREPARES => false,
            // Disable fetching data as strings (fetch as native PHP types)
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);
        // Return the PDO object, representing the established database connection
        return $pdo;
    }
}