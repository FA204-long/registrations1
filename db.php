<?php
$host = "localhost";     // Usually localhost
$dbname = "user_system"; // Name of your database
$user = "root";          // Your MySQL username
$pass = "";              // Your MySQL password (empty if default XAMPP)

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
