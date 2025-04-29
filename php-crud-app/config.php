<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "crud_094";

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8");

// Table name (replace 123 with your last 3 NIM digits)
$table_name = "crud_094";
?>
