<?php
require 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $course_name = $_POST['course_name'];  // Course name (e.g., 'Data Structure')
    $course_id = $_POST['course_id'];      // Course ID (from main course table)
    $section = $_POST['section'];          // Section ('A' or 'B')
    $semester = 2;                         // 2nd Semester

    // Define the maximum number of students per section
    $max_students = 5;

    // Generate the section table name dynamically
    $table_name = "batch_39_{$semester}th_semester_" . strtolower(str_replace(" ", "_", $course_name)) . "_section_" . strtolower($section);

    // Step 1: Check if the section has space (i.e., less than 5 students)
    $result = $conn->query("SELECT current_students FROM $table_name WHERE course_name = '$course_name'");
    
    // Check if the course exists in the section table
    if ($result->num_rows > 0) {
        $section_data = $result->fetch_assoc();
        $current_students_in_section = $section_data['current_students'];

        // Step 2: If the section is full, inform the user
        if ($current_students_in_section >= $max_students) {
            echo "Sorry, Section $section for $course_name is full. Please select another section.";
        } else {
            // Step 3: Register the student in the section
            $student_id = 123; // You would get this from the session or login system

            // Insert the student into the section table
            $conn->query("INSERT INTO $table_name (student_id, course_name) VALUES ('$student_id', '$course_name')");

            // Step 4: Update the `current_students` count in the section table
            $conn->query("UPDATE $table_name SET current_students = current_students + 1 WHERE course_name = '$course_name'");

            // Step 5: Update the `current_students` in the main course table
            $conn->query("UPDATE batch_39_2nd_semester_courses SET current_students = current_students + 1 WHERE id = '$course_id'");

            echo "Registration successful for $course_name in Section $section!";
        }
    } else {
        echo "Course $course_name does not exist in the section $section. Please try again.";
    }
}
?>
