<?php
$conn = new mysqli("localhost", "root", "", "user_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $table_key = $_POST['table_key'];
    $course_name = $_POST['course_name'];
    $section = $_POST['section'];

    // Check if registering for Data Structure
    if ($course_name == "Data Structure") {
        // Check if the student is registered for Structural Programming
        $structural_table_a = "batch_40_2_semester_structural_programming_section_a";
        $structural_table_b = "batch_40_2_semester_structural_programming_section_b";

        $check_structural_sql = "SELECT * FROM `$structural_table_a` WHERE student_id = ? UNION SELECT * FROM `$structural_table_b` WHERE student_id = ?";
        $stmt_check_structural = $conn->prepare($check_structural_sql);
        $stmt_check_structural->bind_param("ss", $student_id, $student_id);
        $stmt_check_structural->execute();
        $check_result_structural = $stmt_check_structural->get_result();

        if ($check_result_structural->num_rows == 0) {
            $message = "❌ You must be registered in Structural Programming before registering for Data Structure.";
        } else {
            // Proceed with Data Structure registration if Structural Programming is checked
            $table_name = "batch_40_3_semester_" . $table_key . "_section_" . strtolower($section);

            $table_check = $conn->query("SHOW TABLES LIKE '$table_name'");
            if ($table_check->num_rows == 0) {
                $message = "❌ Table $table_name does not exist.";
            } else {
                $check_sql = "SELECT * FROM `$table_name` WHERE student_id = ?";
                $stmt_check = $conn->prepare($check_sql);
                $stmt_check->bind_param("s", $student_id);
                $stmt_check->execute();
                $check_result = $stmt_check->get_result();

                if ($check_result->num_rows > 0) {
                    $message = "❌ Already registered in $course_name Section $section.";
                } else {
                    $count_sql = "SELECT COUNT(*) AS total FROM `$table_name`";
                    $count_result = $conn->query($count_sql);
                    $row = $count_result->fetch_assoc();

                    if ($row['total'] >= 5) {
                        $message = "⚠️ Section $section is full.";
                    } else {
                        $insert_sql = "INSERT INTO `$table_name` (student_id) VALUES (?)";
                        $stmt = $conn->prepare($insert_sql);
                        $stmt->bind_param("s", $student_id);
                        if ($stmt->execute()) {
                            $message = "✅ Registered in $course_name (Section $section).";
                        } else {
                            $message = "❌ Registration failed.";
                        }
                        $stmt->close();
                    }
                }
                $stmt_check->close();
            }
        }
        $stmt_check_structural->close();
    } else {
        // For other courses, proceed with normal registration
        $table_name = "batch_40_3_semester_" . $table_key . "_section_" . strtolower($section);

        $table_check = $conn->query("SHOW TABLES LIKE '$table_name'");
        if ($table_check->num_rows == 0) {
            $message = "❌ Table $table_name does not exist.";
        } else {
            $check_sql = "SELECT * FROM `$table_name` WHERE student_id = ?";
            $stmt_check = $conn->prepare($check_sql);
            $stmt_check->bind_param("s", $student_id);
            $stmt_check->execute();
            $check_result = $stmt_check->get_result();

            if ($check_result->num_rows > 0) {
                $message = "❌ Already registered in $course_name Section $section.";
            } else {
                $count_sql = "SELECT COUNT(*) AS total FROM `$table_name`";
                $count_result = $conn->query($count_sql);
                $row = $count_result->fetch_assoc();

                if ($row['total'] >= 5) {
                    $message = "⚠️ Section $section is full.";
                } else {
                    $insert_sql = "INSERT INTO `$table_name` (student_id) VALUES (?)";
                    $stmt = $conn->prepare($insert_sql);
                    $stmt->bind_param("s", $student_id);
                    if ($stmt->execute()) {
                        $message = "✅ Registered in $course_name (Section $section).";
                    } else {
                        $message = "❌ Registration failed.";
                    }
                    $stmt->close();
                }
            }
            $stmt_check->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>40th 3rd Semester Registration</title></head>
<body>
<h2>40th 3rd Semester - Course Registration</h2>
<?php if (!empty($message)) echo "<div>$message</div>"; ?>
<?php
$courses = [
    'Algorithm' => 'algorithm',
    'Object Oriented Concept' => 'object_oriented_concept',
    'Bangladesh Studies' => 'bangladesh_studies',
    'Statistics' => 'statistics',
    'Machine Learning' => 'machine_learning'
];
foreach ($courses as $course_name => $table_key):
?>
<form method="POST">
    <input type="hidden" name="course_name" value="<?php echo $course_name; ?>">
    <input type="hidden" name="table_key" value="<?php echo $table_key; ?>">
    <label><?php echo $course_name; ?></label>
    <input type="text" name="student_id" placeholder="Student ID" required>
    <select name="section" required>
        <option value="">Select Section</option>
        <option value="a">A</option>
        <option value="b">B</option>
    </select>
    <button type="submit">Register</button>
</form>
<?php endforeach; ?>
</body>
</html>
