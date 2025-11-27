<?php
session_start();
require_once "config.php";


if (!isset($_SESSION['administrator_id'])) {
    header("Location: LOGIN/login.php");
    exit();
}


if (isset($_POST['add_course'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO courses (title, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $description);
    $stmt->execute();
    $message = "Course added successfully!";
}
?>

<form method="POST">
    <h2>Add Course</h2>
    <input type="text" name="title" placeholder="Course Title" required><br>
    <textarea name="description" placeholder="Course Description" required></textarea><br>
    <button type="submit" name="add_course">Add Course</button>
</form>

<?php if(isset($message)) echo $message; ?>
