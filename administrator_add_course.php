<?php
session_start();
require_once "LOGIN/config.php";

if (!isset($_SESSION['administrator_id'])) {
    header("Location: LOGIN/login.php");
    exit();
}
$professors = $conn->query("SELECT id, name FROM users WHERE role='professor'");
if (isset($_POST['add_course'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $professor_id = $_POST['professor_id'];

    $stmt = $conn->prepare("INSERT INTO courses (title, description, professor_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $description, $professor_id);
    if ($stmt->execute()) {
        $message = "Course added successfully!";
    } else {
        $message = "Error adding course. Try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Course</title>
<link href="header/header.css" rel="stylesheet">
</head>
<body>

<?php include "header/header.php"; ?> 

<h2>Add New Course</h2>
<form method="POST">
    <input type="text" name="title" placeholder="Course Title" required><br>
    <textarea name="description" placeholder="Course Description" required></textarea><br>
    <label for="professor_id">Assign Professor:</label>
    <select name="professor_id" required>
        <option value="">--Select Professor--</option>
        <?php while($prof = $professors->fetch_assoc()): ?>
            <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['name']) ?></option>
        <?php endwhile; ?>
    </select><br>
    <button type="submit" name="add_course">Add Course</button>
</form>

<?php if(isset($message)) echo "<p>$message</p>"; ?>

</body>
</html>
