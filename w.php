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

    // Check if registering for "Object Oriented Design"
    if ($course_name == "Object Oriented Design") {
        // Check if the student is registered for "Object Oriented Concept"
        $concept_table_a = "batch_39_3_semester_object_oriented_concept_section_a";
        $concept_table_b = "batch_39_3_semester_object_oriented_concept_section_b";

        $check_concept_sql = "SELECT * FROM `$concept_table_a` WHERE student_id = ? UNION SELECT * FROM `$concept_table_b` WHERE student_id = ?";
        $stmt_check_concept = $conn->prepare($check_concept_sql);
        $stmt_check_concept->bind_param("ss", $student_id, $student_id);
        $stmt_check_concept->execute();
        $check_result_concept = $stmt_check_concept->get_result();

        if ($check_result_concept->num_rows == 0) {
            $message = "❌ You must be registered in Object Oriented Concept before registering for Object Oriented Design.";
        } else {
            // Proceed with registration for Object Oriented Design if Object Oriented Concept is checked
            $table_name = "batch_39_4_semester_" . $table_key . "_section_" . strtolower($section);
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
                        $stmt->execute();
                        $message = "✅ Registered in $course_name (Section $section).";
                    }
                }
                $stmt_check->close();
            }
        }
        $stmt_check_concept->close();
    } else {
        // For other courses, proceed with normal registration
        $table_name = "batch_39_4_semester_" . $table_key . "_section_" . strtolower($section);
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
                    $stmt->execute();
                    $message = "✅ Registered in $course_name (Section $section).";
                }
            }
            $stmt_check->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>39th 4th Semester Registration</title></head>
<body>
<h2>39th 4th Semester - Course Registration</h2>
<?php if (!empty($message)) echo "<p style='background:#eee;padding:10px;'>$message</p>"; ?>

<?php
$courses = [
    'Algorithm' => 'algorithm',
    'Object Oriented Design' => 'object_oriented_design',
    'Design Pattern' => 'design_pattern',
    'Robotics' => 'robotics',
    'Accounting' => 'accounting'
];

foreach ($courses as $name => $key): ?>
<form method="POST">
    <input type="hidden" name="course_name" value="<?php echo $name; ?>">
    <input type="hidden" name="table_key" value="<?php echo $key; ?>">
    <label>Course: <b><?php echo $name; ?></b></label>
    <input type="text" name="student_id" placeholder="Student ID" required>
    <select name="section" required>
        <option value="">Select Section</option>
        <option value="a">A</option>
        <option value="b">B</option>
    </select>
    <button type="submit">Register</button>
</form>
<hr>
<?php endforeach; ?>
</body>
</html>
