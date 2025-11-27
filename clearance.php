<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

require 'db.php'; // Include database connection

$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['id'];
    $clearance_no = $_POST['clearance_no'];
    $semester = $_POST['semester'];

    // Prepare and insert into database
    $stmt = $conn->prepare("INSERT INTO clearance (student_id, clearance_no, semester) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $student_id, $clearance_no, $semester);

    if ($stmt->execute()) {
        $success = "Clearance submitted successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Clearance Form</title>
  <link rel="stylesheet" href="dashboard.css">
  <style>
    .form-box {
      margin: 60px auto;
      background: rgba(255, 255, 255, 0.1);
      padding: 40px;
      border-radius: 15px;
      width: 350px;
      backdrop-filter: blur(8px);
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      color: white;
      text-align: center;
    }

    .form-box h2 {
      margin-bottom: 25px;
    }

    .form-box input[type="text"],
    .form-box input[type="submit"] {
      width: 100%;
      padding: 10px;
      margin: 12px 0;
      border: none;
      border-radius: 8px;
      font-size: 15px;
    }

    .form-box input[type="text"] {
      background: rgba(255,255,255,0.2);
      color: white;
      outline: none;
    }

    .form-box input[type="submit"] {
      background: #00c3ff;
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .form-box input[type="submit"]:hover {
      background: #0099cc;
    }

    .message {
      margin-top: 10px;
      font-size: 14px;
    }

    .success { color: lightgreen; }
    .error { color: red; }
  </style>
</head>
<body>

  <div class="navbar">
    <h2 class="brand">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
    <ul>
      <li><a href="dashboard.php">Home</a></li>
      <li><a href="clearance.php">Clearance</a></li>
      <li><a href="clearance_view.php">View Records</a></li>
      <li><a href="registration.php">Registration</a></li>
      <li><a href="#">Result</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="form-box">
    <h2>Clearance Form</h2>
    <form method="post" action="">
      <input type="text" name="id" placeholder="Enter ID" required>
      <input type="text" name="clearance_no" placeholder="Clearance Number" required>
      <input type="text" name="semester" placeholder="Semester" required>
      <input type="submit" value="Submit">
    </form>

    <?php if ($success): ?>
      <p class="message success"><?php echo $success; ?></p>
    <?php elseif ($error): ?>
      <p class="message error"><?php echo $error; ?></p>
    <?php endif; ?>
  </div>

</body>
</html>
