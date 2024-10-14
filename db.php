<?php
// Database connection settings
$servername = "127.0.0.1";  // Change this if the database is hosted elsewhere
$username = "root";
$password = "";
$dbname = "mysql";  // Replace with your actual database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
