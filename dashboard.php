<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>

  <div class="navbar">
    <h2 class="brand">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
    <ul>
      <li><a href="#">Home</a></li>
      <li><a href="clearance.php">Clearance</a></li>
      <li><a href="clearance_view.php">View Records</a></li>
      <li><a href="registration.php">Registration</a></li>
      <li><a href="registration.php">Result</a></li>
      <li><a href="logout.php">Logout</a></li>
      

    </ul>
  </div>

  <div class="content">
    <h1>Dashboard</h1>
    <p>This is your student dashboard. Choose a section from the navigation bar.</p>
  </div>

</body>
</html>
