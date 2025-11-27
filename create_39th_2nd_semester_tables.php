<?php
$conn = new mysqli("localhost", "root", "", "your_database_name"); // Change this to your DB

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$courses = [
    'Data Structures',
    'Digital Logic Design',
    'Discrete Mathematics',
    'Professional Communication'
];

$sections = ['a', 'b'];
$semester = '2nd';

foreach ($courses as $course) {
    $course_key = strtolower(str_replace(' ', '_', $course));

    foreach ($sections as $section) {
        $table_name = "batch_39_{$semester}_semester_{$course_key}_section_{$section}";

        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id VARCHAR(50) NOT NULL
        )";

        if ($conn->query($sql) === TRUE) {
            echo "✅ Table created or already exists: $table_name<br>";
        } else {
            echo "❌ Error creating table $table_name: " . $conn->error . "<br>";
        }
    }
}

$conn->close();
?>
