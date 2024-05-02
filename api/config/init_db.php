<?php
// Include the database configuration file
include_once 'config.php';

//  Function for executing SQL queries on the database
function executeQuery($connection, $sql_query) {
    if (mysqli_query($connection, $sql_query)) {
        echo "Success";
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}

try {

    // Create and check connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Close connection when done
    $conn->close();
} catch (Exception $e) {
    // If connection fails, display the error message
    echo "Error: " . $e->getMessage();
}

// SQL queries to create tables
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$sql_recipes = "CREATE TABLE IF NOT EXISTS recipes (
    recipe_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    recipe_name VARCHAR(255) NOT NULL,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    recipe_description TEXT NOT NULL,
    visibility TINYINT,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";

$sql_recipe_books = "CREATE TABLE IF NOT EXISTS recipe_books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    visibility TINYINT,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";

$sql_user_favorites = "CREATE TABLE IF NOT EXISTS user_favorites (
    user_id INT,
    recipe_id INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id)
  )";

// Execute SQL queries
executeQuery($conn, $sql_users);
executeQuery($conn, $sql_recipes);
executeQuery($conn, $sql_recipe_books);
executeQuery($conn, $sql_user_favorites);

// Close database connection
mysqli_close($conn);

