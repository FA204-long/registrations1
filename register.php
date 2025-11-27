<?php
require 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $user_id = htmlspecialchars($_POST['user_id']);
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check for existing user_id or email
    $check = $conn->prepare("SELECT id FROM users WHERE user_id = ? OR email = ?");
    $check->bind_param("ss", $user_id, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "User ID or Email already exists!";
        $check->close();
        exit;
    }
    $check->close();

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (user_id, username, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user_id, $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
