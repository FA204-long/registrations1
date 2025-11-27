<?php
require 'db.php'; // Database connection

$clearance_status = false;
$clearance_no = '';
$batch = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clearance_no = $_POST['clearance_no'];
    $batch = $_POST['batch'];

    $result = $conn->query("SELECT * FROM clearance WHERE clearance_no = '$clearance_no'");

    if ($result && $result->num_rows > 0) {
        $clearance_status = true;
    } else {
        echo "<p class='error'>Invalid Clearance Number.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Course Registration</title>
  
  <style>
    body {
      font-family: Arial, sans-serif;
      background: url('diu.jpg') no-repeat center center fixed;
      background-size: cover;
      margin: 0;
    }

    .navbar {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      padding: 10px 0;
      position: right;
      width: 100%;
      top: 0;
      z-index: 1000;
    }

    .navbar ul {
      list-style-type: none;
      margin: 0;
      padding: 0;
      overflow: hidden;
      text-align: right;
    }

    .navbar li {
      display: inline;
    }

    .navbar a {
      color: white;
      padding: 14px 20px;
      text-decoration: none;
      display: inline-block;
    }

    .navbar a:hover {
      background-color: #ddd;
      color: black;
    }

    .form-box {
      background: rgba(255, 255, 255, 0.1);
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      width: 80%;
      max-width: 600px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      margin: 80px auto 0;
    }

    h2, h3 {
      text-align: center;
      color: #fff;
    }

    label {
      font-size: 16px;
      margin-bottom: 10px;
      color: #fff;
      display: block;
    }

    input, select {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid rgba(255, 255, 255, 0.5);
      background: rgba(255, 255, 255, 0.3);
      color: black;
      font-size: 16px;
    }

    button {
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
      padding: 10px 20px;
      border-radius: 8px;
      width: 100%;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #45a049;
    }

    .semester {
      margin-bottom: 15px;
    }

    .error {
      color: red;
      text-align: center;
    }

    @media (max-width: 768px) {
      .form-box {
        width: 100%;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

  <div class="navbar">
    <ul>
      <li><a href="dashboard.php">Home</a></li>
      <li><a href="clearance.php">Clearance</a></li>
      <li><a href="registration.php">Registration</a></li>
      <li><a href="result.php">Result</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="form-box">
    <h2>Course Registration</h2>

    <?php if (!$clearance_status): ?>
        <form action="registration.php" method="POST">
            <label for="clearance_no">Enter Your Clearance No:</label>
            <input type="text" name="clearance_no" id="clearance_no" placeholder="Enter Clearance No" required>

            <label for="batch">Select Batch:</label>
            <select name="batch" id="batch" required>
                <option value="" disabled selected>Select Batch</option>
                <option value="39">Batch 39</option>
                <option value="40">Batch 40</option>
            </select>

            <button type="submit">Submit</button>
        </form>
    <?php else: ?>
        <h3>Available Semesters for Batch <?php echo $batch; ?></h3>

        <div class="semester-list">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <?php
                    $suffix = match($i) {
                        1 => "1st",
                        2 => "2nd",
                        3 => "3rd",
                        default => "{$i}th"
                    };
                    $target_file = "{$batch}th_{$i}_semester.php";
                ?>
                <div class="semester">
                    <p><?php echo $batch; ?>th <?php echo $suffix; ?> Semester</p>
                    <form action="<?php echo $target_file; ?>" method="GET">
                        <button type="submit">Enroll</button>
                    </form>
                </div>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
  </div>

</body>
</html>
