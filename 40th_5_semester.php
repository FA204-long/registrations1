<?php
$conn = new mysqli("localhost", "root", "", "user_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle Registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $student_id = $_POST['student_id'];
    $table_key = $_POST['table_key'];
    $course_name = $_POST['course_name'];
    $section = $_POST['section'];

    // Check if registering for "Business Studies"
    if ($course_name == "Business Studies") {
        // Check if the student is registered for "Accounting"
        $accounting_table_a = "batch_40_4_semester_accounting_section_a";
        $accounting_table_b = "batch_40_4_semester_accounting_section_b";

        $check_accounting_sql = "SELECT * FROM `$accounting_table_a` WHERE student_id = ? UNION SELECT * FROM `$accounting_table_b` WHERE student_id = ?";
        $stmt_check_accounting = $conn->prepare($check_accounting_sql);
        $stmt_check_accounting->bind_param("ss", $student_id, $student_id);
        $stmt_check_accounting->execute();
        $check_result_accounting = $stmt_check_accounting->get_result();

        if ($check_result_accounting->num_rows == 0) {
            $message = "❌ You must be registered in Accounting before registering for Business Studies.";
        } else {
            // Proceed with Business Studies registration if Accounting is checked
            $table_name = "batch_40_5_semester_" . $table_key . "_section_" . strtolower($section);

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
        $stmt_check_accounting->close();
    } else {
        // For other courses, proceed with normal registration
        $table_name = "batch_40_5_semester_" . $table_key . "_section_" . strtolower($section);

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

// Handle Unregistration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unregister'])) {
    $student_id = $_POST['student_id'];
    $table_key = $_POST['table_key'];
    $course_name = $_POST['course_name'];
    $section = $_POST['section'];

    // Dynamically create the table name based on course and section
    $table_name = "batch_40_5_semester_" . $table_key . "_section_" . strtolower($section);

    // Check if the student is already registered in the specified course section
    $check_sql = "SELECT * FROM `$table_name` WHERE student_id = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $student_id);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows > 0) {
        $delete_sql = "DELETE FROM `$table_name` WHERE student_id = ?";
        $stmt_delete = $conn->prepare($delete_sql);
        $stmt_delete->bind_param("s", $student_id);
        if ($stmt_delete->execute()) {
            $message = "✅ Unregistered from $course_name (Section $section).";
        } else {
            $message = "❌ Unregistration failed.";
        }
        $stmt_delete->close();
    } else {
        $message = "❌ You are not registered for $course_name (Section $section).";
    }
    $stmt_check->close();
}
?>

<!DOCTYPE html>
<html>
<head><title>40th 5th Semester Registration</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: url('diu.jpg') no-repeat center center fixed;
        background-size: cover;
        padding: 30px;
    }
    .container {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 20px;
        border-radius: 15px;
        width: 80%;
        max-width: 700px;
        margin: auto;
        color: black;
    }
    h2 { text-align: center; color: #333; }
    form {
        margin-bottom: 20px;
        padding: 15px;
        background-color: rgba(0, 0, 0, 0.6);
        border-radius: 10px;
    }
    label, select, input {
        display: block;
        margin: 10px 0;
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        font-size: 16px;
    }
    button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
    }
    .message {
        padding: 10px;
        background: #f8d7da;
        color: #721c24;
        border-radius: 8px;
        margin-top: 15px;
    }
    .unregister-btn {
        background-color: red;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
    }
    .unregistered-btn, .registered-btn {
        background-color: gray;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: not-allowed;
    }
</style>
</head>
<body>
<div class="container">
    <h2>40th 5th Semester - Course Registration</h2>
    <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>

    <?php
    $courses = [
        'Networking' => 'networking',
        'Networking Lab' => 'networking_lab',
        'Business Studies' => 'business_studies',
        'Software Testing' => 'software_testing'
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
        <button type="submit" name="register">Register</button>
    </form>

    <!-- Unregistration Form -->
    <form method="POST">
        <input type="hidden" name="course_name" value="<?php echo $course_name; ?>">
        <input type="hidden" name="table_key" value="<?php echo $table_key; ?>">
        <input type="text" name="student_id" placeholder="Student ID" required>
        <select name="section" required>
            <option value="">Select Section</option>
            <option value="a">A</option>
            <option value="b">B</option>
        </select>
        <button type="submit" name="unregister" class="unregister-btn">Unregister</button>
    </form>

    <?php endforeach; ?>
</div>
</body>
</html>
