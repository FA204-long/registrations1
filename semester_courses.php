<?php
require 'db.php'; // Database connection

$batch = $_GET['batch'];  // Get batch (39, 40, etc.)
$semester = $_GET['semester']; // Get semester (1-5)

// Function to get ordinal suffix for the semester (1st, 2nd, 3rd, etc.)
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

// Generate the table name for the semester
$semester_table = "batch_{$batch}_" . getOrdinal($semester) . "_semester_courses";

// Query to fetch courses for that semester and batch
$result = $conn->query("SELECT * FROM $semester_table");

if ($result && $result->num_rows > 0) {
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
} else {
    echo "<p class='error'>No courses found for the selected semester and batch.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Registration - Semester <?php echo $semester; ?></title>
    <style>
        /* Add your styles here */
    </style>
</head>
<body>

    <div class="form-box">
        <h2>Courses for Batch <?php echo $batch; ?> - Semester <?php echo getOrdinal($semester); ?></h2>

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
                            <!-- Select section (A, B) -->
                            <select name="section_<?php echo $course['id']; ?>" id="section_<?php echo $course['id']; ?>" required>
                                <option value="" disabled selected>Select Section</option>
                                <option value="A">Section A</option>
                                <option value="B">Section B</option>
                            </select>
                        </td>
                        <td>
                            <button class="course-btn" id="register_<?php echo $course['id']; ?>" onclick="registerCourse('<?php echo $course['course_name']; ?>', '<?php echo $course['id']; ?>')">Register</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function registerCourse(course_name, course_id) {
            const section = document.getElementById(`section_${course_id}`).value;
            const button = document.getElementById(`register_${course_id}`);

            if (!section) {
                alert('Please select a section!');
                return;
            }

            // Disable the button and change its text to "Registering..."
            button.classList.add('disabled');
            button.textContent = "Registering...";

            // Send course_name, course_id, and section to register_course.php via AJAX
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "register_course.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("course_name=" + encodeURIComponent(course_name) + "&course_id=" + course_id + "&section=" + section);

            xhr.onload = function() {
                if (xhr.status == 200) {
                    // On success, change button text to "Registered"
                    button.classList.remove('disabled');
                    button.classList.add('registered');
                    button.textContent = "Registered";
                } else {
                    alert("Error: " + xhr.status);
                    button.classList.remove('disabled');
                    button.textContent = "Register";
                }
            };
        }
    </script>

</body>
</html>
