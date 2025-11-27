<?php
session_start();
require 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];  // Username input
    $password = $_POST['password'];  // Password input

    // Query to check if the user exists in the database
    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the password matches the hashed password in the database
        if (password_verify($password, $user['password'])) {
            // If the password matches, start the session and store user_id
            $_SESSION['user_id'] = $user['id'];  // Store user_id in session
            $_SESSION['username'] = $user['username'];  // Store username in session

            // Redirect to the dashboard or registration page
            header("Location: dashboard.php");  // Redirect to the registration page
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "Invalid username!";
    }
}
?>

<!-- HTML form for login -->
<form action="login.php" method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Login</button>
</form>
