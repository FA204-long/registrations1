<?php
$conn = new mysqli("localhost", "root", "", "user_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle registration and unregistration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $table_key = $_POST['table_key'];
    $course_name = $_POST['course_name'];
    $section = $_POST['section'];

    // Check if registering for "Business Studies"
    if ($course_name == "Business Studies") {
        // Check if the student is registered for "Accounting"
        $accounting_table_a = "batch_39_4_semester_accounting_section_a";
        $accounting_table_b = "batch_39_4_semester_accounting_section_b";

        $check_accounting_sql = "SELECT * FROM `$accounting_table_a` WHERE student_id = ? UNION SELECT * FROM `$accounting_table_b` WHERE student_id = ?";
        $stmt_check_accounting = $conn->prepare($check_accounting_sql);
        $stmt_check_accounting->bind_param("ss", $student_id, $student_id);
        $stmt_check_accounting->execute();
        $check_result_accounting = $stmt_check_accounting->get_result();

        if ($check_result_accounting->num_rows == 0) {
            $message = "❌ You must be registered in Accounting before registering for Business Studies.";
        } else {
            // Proceed with Business Studies registration if Accounting is checked
            $table_name = "batch_39_5_semester_" . $table_key . "_section_" . strtolower($section);
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
                    // Student is already registered, show unregistration
                    if (isset($_POST['unregister'])) {
                        // Unregister the student
                        $delete_sql = "DELETE FROM `$table_name` WHERE student_id = ?";
                        $stmt_delete = $conn->prepare($delete_sql);
                        $stmt_delete->bind_param("s", $student_id);
                        $stmt_delete->execute();
                        $message = "✅ Unregistered from $course_name (Section $section).";
                    }
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
        $stmt_check_accounting->close();
    } else {
        // For other courses, proceed with normal registration
        $table_name = "batch_39_5_semester_" . $table_key . "_section_" . strtolower($section);
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
                // Student is already registered, show unregistration
                if (isset($_POST['unregister'])) {
                    // Unregister the student
                    $delete_sql = "DELETE FROM `$table_name` WHERE student_id = ?";
                    $stmt_delete = $conn->prepare($delete_sql);
                    $stmt_delete->bind_param("s", $student_id);
                    $stmt_delete->execute();
                    $message = "✅ Unregistered from $course_name (Section $section).";
                }
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
<head><title>39th 5th Semester Registration</title></head>
<body>
<h2>39th 5th Semester - Course Registration</h2>
<?php if (!empty($message)) echo "<p style='background:#eee;padding:10px;'>$message</p>"; ?>

<?php
$courses = [
    'Networking' => 'networking',
    'Networking Lab' => 'networking_lab',
    'Business Studies' => 'business_studies',
    'Software Testing' => 'software_testing'
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
    
    <?php
    // Check if the student is already registered
    $table_name = "batch_39_5_semester_" . $key . "_section_a"; // Assuming section 'a' for checking
    $check_sql = "SELECT * FROM `$table_name` WHERE student_id = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $_POST['student_id']);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();
    $is_registered = $check_result->num_rows > 0;
    ?>

    <?php if ($is_registered): ?>
        <button type="submit" name="unregister" class="unregister-btn">Unregister</button>
    <?php else: ?>
        <button type="submit">Register</button>
    <?php endif; ?>
</form>
<hr>
<?php endforeach; ?>
</body>
</html>
