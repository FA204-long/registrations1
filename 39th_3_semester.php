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

    // Check if registering for "Data Structure"
    if ($course_name == "Data Structure") {
        // Check if the student is registered for Structural Programming
        $structural_table_a = "batch_39_2nd_semester_structural_programming_section_a";
        $structural_table_b = "batch_39_2nd_semester_structural_programming_section_b";
        
        $check_structural_sql = "SELECT * FROM `$structural_table_a` WHERE student_id = ? UNION SELECT * FROM `$structural_table_b` WHERE student_id = ?";
        $stmt_check_structural = $conn->prepare($check_structural_sql);
        $stmt_check_structural->bind_param("ss", $student_id, $student_id);
        $stmt_check_structural->execute();
        $check_result_structural = $stmt_check_structural->get_result();
        
        if ($check_result_structural->num_rows == 0) {
            $message = "❌ You must be registered in Structural Programming before registering for Data Structure.";
        } else {
            // Proceed with registering in Data Structure if Structural Programming is checked
            $table_name = "batch_39_3_semester_" . $table_key . "_section_" . strtolower($section);

            $table_check = $conn->query("SHOW TABLES LIKE '$table_name'");
            if ($table_check->num_rows == 0) {
                $message = "❌ Table $table_name does not exist.";
            } else {
                // Check if student is already registered
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
        $stmt_check_structural->close();
    } else {
        // For other courses, proceed with normal registration
        $table_name = "batch_39_3_semester_" . $table_key . "_section_" . strtolower($section);
        $table_check = $conn->query("SHOW TABLES LIKE '$table_name'");

        if ($table_check->num_rows == 0) {
            $message = "❌ Table $table_name does not exist.";
        } else {
            // Check if student is already registered
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
<head>
    <title>39th 3rd Semester Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('diu.jpg') no-repeat center center fixed;
            background-size: cover;
            padding: 30px;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 20px;
            border-radius: 15px;
            width: 80%;
            max-width: 700px;
            margin: auto;
            color: white;
        }
        h2 { text-align: center; }
        form {
            margin-bottom: 20px;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.2);
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
            background: white;
            color: black;
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
    <h2>39th 3rd Semester - Course Registration</h2>

    <?php
    if (!empty($message)) {
        echo "<div class='message'>$message</div>";
    }

    $courses = [
        'Data Structure' => 'data_structure',
        'Algorithm' => 'algorithm',
        'Object Oriented Concept' => 'object_oriented_concept',
        'Object Oriented Design' => 'object_oriented_design',
        'Bangladesh Studies' => 'bangladesh_studies',
        'Statistics' => 'statistics',
        'Machine Learning' => 'machine_learning'
    ];

    foreach ($courses as $course_name => $table_key):
    ?>
    <form method="POST">
        <input type="hidden" name="course_name" value="<?php echo $course_name; ?>">
        <input type="hidden" name="table_key" value="<?php echo $table_key; ?>">

        <label>Course: <strong><?php echo $course_name; ?></strong></label>

        <label for="student_id">Student ID:</label>
        <input type="text" name="student_id" required>

        <label for="section">Select Section:</label>
        <select name="section" required>
            <option value="">Select</option>
            <option value="a">Section A</option>
            <option value="b">Section B</option>
        </select>

        <?php
        // Check if the student is already registered
        $table_name = "batch_39_3_semester_" . $table_key . "_section_a"; // Assuming section 'a' for checking
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
</div>
</body>
</html>
