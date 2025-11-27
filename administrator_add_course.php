<?php
session_start();
require_once "LOGIN/config.php";

include "header/header.php";

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
        $message = "Error adding course.";
    }
}
$courses = $conn->query("
    SELECT courses.*, users.name AS professor_name 
    FROM courses 
    LEFT JOIN users ON courses.professor_id = users.id
    ORDER BY courses.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Course</title>

</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #eef2f7;
        margin: 0;
        padding: 0;
    }

    .page-container {
        width: 90%;
        max-width: 1000px;
        margin: 40px auto;
    }
    .course-form {
        background: white;
        padding: 25px;
        border-radius: 12px;
        width: 100%;
        max-width: 500px;
        margin: 0 auto 40px auto;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .course-form h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #34495e;
    }

    input,
    textarea,
    select {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 15px;
        transition: 0.2s;
    }

    input:focus,
    textarea:focus,
    select:focus {
        border-color: #3498db;
        outline: none;
        box-shadow: 0 0 5px rgba(52,152,219,0.4);
    }

    textarea {
        height: 100px;
        resize: none;
    }

    button {
        width: 100%;
        padding: 12px;
        background: #3498db;
        border: none;
        color: white;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.2s;
    }

    button:hover {
        background: #217dbb;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
        color: #555;
    }

    .message {
        text-align: center;
        font-weight: bold;
        color: green;
        margin-bottom: 20px;
        font-size: 18px;
    }
    .courses-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .courses-table th {
        background: #3498db;
        color: white;
        padding: 12px;
        font-size: 16px;
    }

    .courses-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        font-size: 15px;
    }

    .courses-table tr:hover {
        background: #f5faff;
    }

    h2 {
        color: #34495e;
        margin-bottom: 15px;
        text-align: center;
    }
</style>

<body>

<div class="page-container">

    <form method="POST" class="course-form">
        <h2>Add New Course</h2>

        <input type="text" name="title" placeholder="Course Title" required>
        <textarea name="description" placeholder="Course Description" required></textarea>

        <label>Assign Professor:</label>
        <select name="professor_id" required>
            <option value="">-- Select Professor --</option>
            <?php while ($prof = $professors->fetch_assoc()): ?>
                <option value="<?= $prof['id'] ?>">
                    <?= htmlspecialchars($prof['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit" name="add_course">Add Course</button>
    </form>

    <?php if (isset($message)) : ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <h2>All Courses</h2>

    <table class="courses-table">
        <tr>
            <th>ID</th>
            <th>Course Title</th>
            <th>Description</th>
            <th>Professor</th>
        </tr>

        <?php while ($row = $courses->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= $row['professor_name'] ? $row['professor_name'] : "Not Assigned" ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

</div>

</body>
</html>
