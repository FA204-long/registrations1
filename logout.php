<?php
session_start();
session_destroy(); // End all sessions
header("Location: index.html"); // Redirect to login
exit();
?>
