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

    // Logic for registering a course (same as the code you provided)
    if ($course_name == "Object Oriented Design") {
        // Check if the student is registered for Object Oriented Concept before registering for Object Oriented Design
        $concept_table_a = "batch_40_3_semester_object_oriented_concept_section_a";
        $concept_table_b = "batch_40_3_semester_object_oriented_concept_section_b";
        $check_concept_sql = "SELECT * FROM `$concept_table_a` WHERE student_id = ? UNION SELECT * FROM `$concept_table_b` WHERE student_id = ?";
        $stmt_check_concept = $conn->prepare($check_concept_sql);
        $stmt_check_concept->bind_param("ss", $student_id, $student_id);
        $stmt_check_concept->execute();
        $check_result_concept = $stmt_check_concept->get_result();
        
        if ($check_result_concept->num_rows == 0) {
            $message = "❌ You must be registered in Object Oriented Concept before registering for Object Oriented Design.";
        } else {
            // Proceed with Object Oriented Design registration if Object Oriented Concept is checked
            $table_name = "batch_40_4_semester_" . $table_key . "_section_" . strtolower($section);
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
        $stmt_check_concept->close();
    } else {
        // For other courses, proceed with normal registration
        $table_name = "batch_40_4_semester_" . $table_key . "_section_" . strtolower($section);

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
    $table_name = "batch_40_4_semester_" . $table_key . "_section_" . strtolower($section);

    // Check if the student is already registered in the specified course section
    $check_sql = "SELECT * FROM `$table_name` WHERE student_id = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $student_id);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows > 0) {
        // Proceed with unregistration if student is found
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
        // If student is not found in the table
        $message = "❌ You are not registered for $course_name (Section $section).";
    }
    $stmt_check->close();
}

?>

<!DOCTYPE html>
<html>
<head><title>40th 4th Semester Registration</title>
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
    <h2>40th 4th Semester - Course Registration</h2>
    <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>

    <?php
    $courses = [
        'Algorithm' => 'algorithm',
        'Object Oriented Design' => 'object_oriented_design',
        'Design Pattern' => 'design_pattern',
        'Robotics' => 'robotics',
        'Accounting' => 'accounting'
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
