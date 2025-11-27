<?php
require 'db.php'; // Database connection

$batch = $_GET['batch'];  // Get batch (39, 40, etc.)
$semester = $_GET['semester']; // Get semester (1-5)

// Function to get ordinal suffix
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

// Correctly generate the table name for the semester
$semester_table = "batch_{$batch}_" . getOrdinal($semester) . "_semester_courses";  // e.g., batch_39_1st_semester_courses

// Query to fetch courses for that semester and batch
$result = $conn->query("SELECT * FROM $semester_table");

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

// Function to get the section availability for a course
function getSectionAvailability($course_id, $section) {
    global $conn;
    $section_table = "course_{$course_id}_{$section}_registrations";
    $result = $conn->query("SELECT COUNT(*) AS student_count FROM $section_table");

    if ($result) {
        $data = $result->fetch_assoc();
        return $data['student_count'];
    }

    return 0;
}

// Function to get the maximum number of students for a course section
function getMaxStudents($course_id) {
    global $conn;
    $result = $conn->query("SELECT max_students FROM $course_id");

    if ($result) {
        $data = $result->fetch_assoc();
        return $data['max_students'];
    }

    return 0;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>39th Batch - 1st Semester Courses</title>
  <style>
    /* Add your styles here */
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .form-box {
      background-color: #fff;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
      width: 80%;
      max-width: 800px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    table th, table td {
      padding: 15px;
      text-align: center;
      border: 1px solid #ddd;
      font-size: 16px;
    }

    table th {
      background-color: #f2f2f2;
    }

    .btn {
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }

    .btn.disabled {
      background-color: #ddd;
      cursor: not-allowed;
    }

    .btn.registered {
      background-color: #007BFF;
    }

    .btn:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>

  <div class="form-box">
    <h2>39th Batch - 1st Semester</h2>

    <!-- Table displaying courses -->
    <table>
      <thead>
        <tr>
          <th>Course Name</th>
          <th>Section</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($courses as $course): ?>
          <tr>
            <td><?php echo $course['course_name']; ?></td>
            <td>
              <!-- Select section (A, B, C) -->
              <select name="section_<?php echo $course['id']; ?>" id="section_<?php echo $course['id']; ?>" required>
                <option value="" disabled selected>Select Section</option>
                <?php
                $sections = ['A', 'B', 'C']; // Example sections
                foreach ($sections as $section): ?>
                  <option value="<?php echo $section; ?>"><?php echo $section; ?></option>
                <?php endforeach; ?>
              </select>
            </td>
            <td>
              <?php
                $max_students = getMaxStudents($course['id']);
                $can_register = false;
                $buttons = '';
                foreach ($sections as $section) {
                    $students_in_section = getSectionAvailability($course['id'], $section);
                    if ($students_in_section < $max_students) {
                        $can_register = true;
                        $buttons .= '<button class="btn" onclick="enroll(\'' . $course['id'] . '\', \'' . $section . '\')">Enroll</button>';
                    }
                }

                if (!$can_register) {
                    $buttons .= '<button class="btn disabled" disabled>Full</button>';
                }

                echo $buttons;
              ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <script>
    function enroll(course_id, section) {
      // Perform registration logic via AJAX
      const button = event.target;
      button.classList.add("registered");
      button.textContent = "Registered";

      // Update the backend with the course_id and section
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "register_course.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send("course_id=" + course_id + "&section=" + section);

      xhr.onload = function() {
        if (xhr.status == 200) {
          alert("Course Registered Successfully!");
        } else {
          alert("Error: " + xhr.status);
        }
      }
    }
  </script>

</body>
</html>
