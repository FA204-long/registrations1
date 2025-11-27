<?php
$conn = new mysqli("localhost", "root", "", "user_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$registered_section = ""; // Variable to store the section (A or B) where the student is registered
$unregistered = false; // Flag to check if the student is unregistered
$registered = false; // Flag to check if the student is registered

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $table_key = $_POST['table_key'];
    $course_name = $_POST['course_name'];
    $section = $_POST['section'] ?? '';  // Using null coalescing operator to avoid undefined array key warning
    $action = $_POST['action'] ?? ''; // Action to either register or unregister

    // Check registration in both sections (A and B)
    $sections = ['a', 'b']; // Sections to check
    foreach ($sections as $sec) {
        $table_name = "batch_40_2_semester_" . $table_key . "_section_" . strtolower($sec); // Construct table name for the section

        // Check if the student is already registered in this section
        $check_sql = "SELECT * FROM `$table_name` WHERE student_id = ?";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("s", $student_id); // Bind student ID
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();

        if ($check_result->num_rows > 0) {
            // If the student is found, store the section where they are registered
            $registered_section = strtoupper($sec); // Store section (A or B)
            $registered = true; // Set registered flag to true
            break; // Exit loop once we've found the student's section
        }
        $stmt_check->close(); // Close the statement after each check
    }

    // Unregister the student if the action is 'unregister'
    if ($action == 'unregister' && !empty($registered_section)) {
        $table_name = "batch_40_2_semester_" . $table_key . "_section_" . strtolower($registered_section);
        $delete_sql = "DELETE FROM `$table_name` WHERE student_id = ?";
        $stmt_delete = $conn->prepare($delete_sql);
        $stmt_delete->bind_param("s", $student_id);
        
        if ($stmt_delete->execute()) {
            $message = "Successfully unregistered from Section $registered_section for $course_name.";
            $unregistered = true; // Set the flag to true after successful unregistration
        } else {
            $message = "Unregistration failed.";
        }
        $stmt_delete->close();
    } 
    // Proceed with registration if the student is not already registered
    else if (empty($registered_section)) {
        // Register the student in the selected section if they are not already registered
        $table_name = "batch_40_2_semester_" . $table_key . "_section_" . strtolower($section);

        $table_check = $conn->query("SHOW TABLES LIKE '$table_name'");
        if ($table_check->num_rows == 0) {
            $message = "Table $table_name does not exist.";
        } else {
            // Check if the section has space (max 5 students)
            $count_sql = "SELECT COUNT(*) AS total FROM `$table_name`";
            $count_result = $conn->query($count_sql);
            $row = $count_result->fetch_assoc();

            if ($row['total'] >= 5) {
                $message = "Section $section is full.";
            } else {
                // Register the student in the selected section
                $insert_sql = "INSERT INTO `$table_name` (student_id) VALUES (?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("s", $student_id);

                if ($stmt->execute()) {
                    $message = "Successfully registered in $course_name Section " . strtoupper($section) . ".";
                    $registered = true; // Set registered flag to true after successful registration
                } else {
                    $message = "Registration failed.";
                }
                $stmt->close();
            }
        }
    } else {
        $message = "Student is already registered in Section $registered_section for $course_name.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>40th 2nd Semester Registration</title>
    <style>
        body {
            font-family: Arial;
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
    <h2>40th 2nd Semester - Course Registration</h2>

    <?php
    if (!empty($message)) {
        echo "<div class='message'>$message</div>";
    }

    $courses = [
        'Data Structure' => 'data_structure',
        'Digital Electronics' => 'digital_electronics',
        'Physics' => 'physics',
        'Structural Programming' => 'structural_programming'
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
        // Show Register/Registered Button based on the registration status
        if ($registered) {
            echo "<button type='submit' class='registered-btn' disabled>Registered</button>";
        } else {
            echo "<button type='submit'>Register</button>";
        }
        ?>
    </form>

    <?php 
    // Show Unregister Button if already registered
    if (!empty($registered_section) && !$unregistered) {
        echo "<form method='POST'>
                <input type='hidden' name='course_name' value='$course_name'>
                <input type='hidden' name='table_key' value='$table_key'>
                <input type='hidden' name='student_id' value='" . $_POST['student_id'] . "'>
                <input type='hidden' name='action' value='unregister'>
                <button type='submit' class='unregister-btn'>Unregister</button>
              </form>";
    } elseif ($unregistered) {
        echo "<button class='unregistered-btn' disabled>Unregistered</button>";
    }
    ?>
    <?php endforeach; ?>
</div>
</body>
</html>
