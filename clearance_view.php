<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

require 'db.php'; // Database connection

// Fetch all clearance records
$result = $conn->query("SELECT * FROM clearance ORDER BY submitted_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Clearance Records</title>
  <link rel="stylesheet" href="dashboard.css">
  <style>
    .records-box {
      margin: 60px auto;
      background: rgba(255, 255, 255, 0.1);
      padding: 30px;
      border-radius: 15px;
      width: 90%;
      max-width: 900px;
      backdrop-filter: blur(8px);
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      color: white;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      color: white;
    }

    th, td {
      padding: 12px 15px;
      border: 1px solid rgba(255,255,255,0.2);
      text-align: center;
    }

    th {
      background-color: rgba(255, 255, 255, 0.2);
      font-weight: bold;
    }

    tr:nth-child(even) {
      background-color: rgba(255,255,255,0.05);
    }
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

  <div class="records-box">
    <h2>Submitted Clearance Records</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Student ID</th>
        <th>Clearance No</th>
        <th>Semester</th>
        <th>Submitted At</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['student_id']); ?></td>
        <td><?php echo htmlspecialchars($row['clearance_no']); ?></td>
        <td><?php echo htmlspecialchars($row['semester']); ?></td>
        <td><?php echo $row['submitted_at']; ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>

</body>
</html>
