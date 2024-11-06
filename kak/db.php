<?php
// Function to create a connection to the MySQL database
function getConnection() {
    $host = 'localhost';
    $db = 'database name';
    $user = 'username';
    $pass = 'password';
    $charset = 'utf8mb4';

    // Data Source Name (DSN) string for the PDO connection
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    // PDO options to configure database interaction
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,         // Error handling: exceptions for database errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,    // Fetch mode: associative array for query results
        PDO::ATTR_EMULATE_PREPARES => false,                 // Disable emulation to use native prepared statements
    ];

   // Attempt to establish the database connection
   try {
    return new PDO($dsn, $user, $pass, $options);  // Create and return a PDO instance
} catch (PDOException $e) {
    // Handle any connection errors
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
}
?>
