<?php
require 'db.php'; // Database connection

$batch = $_GET['batch'];  // Get the batch from the previous page (e.g., 40)
$semester = $_GET['semester']; // Get the semester (1-5)

$courses = [];

// Dynamically select the course table based on batch and semester
$semester_table = "batch_{$batch}_{$semester}th_semester_courses";  // Example: batch_40_1st_semester_courses

// Query to fetch courses for that semester and batch
$result = $conn->query("SELECT * FROM $semester_table");

while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

// Correct the ordinal suffixes
function getOrdinal($number) {
    $suffix = 'th';
    if ($number == 1) {
        $suffix = 'st';
    } elseif ($number == 2) {
        $suffix = 'nd';
    } elseif ($number == 3) {
        $suffix = 'rd';
    }
    return $number . $suffix;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Course Registration - Semester <?php echo $semester; ?> for Batch 40</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .form-box {
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
      width: 90%;
      max-width: 1200px;
      margin: 20px;
    }

    h3 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 24px;
      color: #333;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      border: 1px solid #ddd;
    }

    table th, table td {
      padding: 15px;
      text-align: center;
      border: 1px solid #ddd;
      font-size: 16px;
    }

    table th {
      background-color: #f2f2f2;
      color: #333;
    }

    table tr:hover {
      background-color: #f9f9f9;
    }

    .course-btn {
      padding: 10px 15px;
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 5px;
      font-size: 14px;
    }

    .course-btn:disabled {
      background-color: #ddd;
      cursor: not-allowed;
    }

    .course-btn:hover {
      background-color: #45a049;
    }

    /* Responsive design */
    @media (max-width: 768px) {
      table {
        font-size: 14px;
      }

      .form-box {
        width: 100%;
        padding: 20px;
      }
    }

  </style>
</head>
<body>

  <div class="form-box">
    <h3>Courses for Batch <?php echo $batch; ?> - Semester <?php echo getOrdinal($semester); ?></h3>

    <!-- Create table to display courses -->
    <table>
      <thead>
        <tr>
          <th>Course Name</th>
          <th>Prerequisite</th>
          <th>Current Students</th>
          <th>Max Students</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($courses as $course): ?>
          <tr>
            <td><?php echo $course['course_name']; ?></td>
            <td><?php echo $course['prerequisite'] ? $course['prerequisite'] : 'None'; ?></td>
            <td><?php echo $course['current_students']; ?></td>
            <td><?php echo $course['max_students']; ?></td>
            <td>
              <form action="register_course.php" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                <button type="submit" class="course-btn" <?php if ($course['current_students'] >= $course['max_students']) echo 'disabled'; ?>>Enroll</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
