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
        $table_name = "batch_40_1_semester_" . $table_key . "_section_" . strtolower($sec); // Construct table name for the section

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
        $table_name = "batch_40_1_semester_" . $table_key . "_section_" . strtolower($registered_section);
        $delete_sql = "DELETE FROM `$table_name` WHERE student_id = ?";
        $stmt_delete = $conn->prepare($delete_sql);
        $stmt_delete->bind_param("s", $student_id);
        
        if ($stmt_delete->execute()) {
            $message = "✅ Successfully unregistered from Section $registered_section for $course_name.";
            $unregistered = true; // Set the flag to true after successful unregistration
        } else {
            $message = "❌ Unregistration failed.";
        }
        $stmt_delete->close();
    } 
    // Proceed with registration if the student is not already registered
    else if (empty($registered_section)) {
        // Register the student in the selected section if they are not already registered
        $table_name = "batch_40_1_semester_" . $table_key . "_section_" . strtolower($section);

        $table_check = $conn->query("SHOW TABLES LIKE '$table_name'");
        if ($table_check->num_rows == 0) {
            $message = "❌ Table $table_name does not exist.";
        } else {
            // Check if the section has space (max 5 students)
            $count_sql = "SELECT COUNT(*) AS total FROM `$table_name`";
            $count_result = $conn->query($count_sql);
            $row = $count_result->fetch_assoc();

            if ($row['total'] >= 5) {
                $message = "⚠️ Section $section is full.";
            } else {
                // Register the student in the selected section
                $insert_sql = "INSERT INTO `$table_name` (student_id) VALUES (?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("s", $student_id);

                if ($stmt->execute()) {
                    $message = "✅ Successfully registered in $course_name Section " . strtoupper($section) . ".";
                    $registered = true; // Set registered flag to true after successful registration
                } else {
                    $message = "❌ Registration failed.";
                }
                $stmt->close();
            }
        }
    } else {
        $message = "❌ Student is already registered in Section $registered_section for $course_name.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>40th 1st Semester Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            padding: 30px;
            margin: 0;
            font-size: 16px;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:disabled {
            background-color: gray;
            cursor: not-allowed;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .register-btn {
            background-color: #007bff;
        }
        .unregister-btn {
            background-color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>40th 1st Semester - Course Registration</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, '❌') === false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php
    $courses = [
        'Math' => 'math',
        'English' => 'english',
        'Computer Fundamentals' => 'computer_fundamentals',
        'Introduction to Software Engineering' => 'introduction_to_software_engineering'
    ];
    foreach ($courses as $course_name => $table_key):
    ?>
    <form method="POST">
        <input type="hidden" name="course_name" value="<?php echo $course_name; ?>">
        <input type="hidden" name="table_key" value="<?php echo $table_key; ?>">

        <label for="student_id"><?php echo $course_name; ?> - Student ID:</label>
        <input type="text" name="student_id" placeholder="Enter your Student ID" required>

        <label for="section">Select Section:</label>
        <select name="section" required>
            <option value="">Select Section</option>
            <option value="a">Section A</option>
            <option value="b">Section B</option>
        </select>

        <?php if ($registered): ?>
            <button type="submit" disabled class="register-btn">Registered</button>
        <?php else: ?>
            <button type="submit" class="register-btn">Register</button>
        <?php endif; ?>

        <?php if ($registered && !$unregistered): ?>
            <input type="hidden" name="action" value="unregister">
            <button type="submit" class="unregister-btn">Unregister</button>
        <?php elseif ($unregistered): ?>
            <button disabled class="unregister-btn">Unregistered</button>
        <?php endif; ?>
    </form>
    <?php endforeach; ?>
</div>
</body>
</html>
