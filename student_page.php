<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: LOGIN/login.php");
    exit();
}

require_once "LOGIN/config.php";

$student_id = $_SESSION['student_id'];
$sql = "SELECT c.id, c.title, c.description
        FROM courses c
        JOIN enrollments e ON c.id = e.course_id
        WHERE e.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$courses = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Home</title>
<link href="/header/header.css" rel="stylesheet">
<style>
.course-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.course-table th, .course-table td {
    border: 1px solid #ddd;
    padding: 10px;
}

.course-table th {
    background-color: #f2f2f2;
    text-align: left;
}

.course-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.course-table tr:hover {
    background-color: #e6f7ff;
}

h1, h2 {
    margin-left: 20px;
}
</style>
</head>
<body>

<?php include 'header/header.php'; ?>

<h1>Welcome, <?= htmlspecialchars($_SESSION['name']); ?></h1>
<h2>Your Enrolled Courses:</h2>

<?php if (!empty($courses)): ?>
<table class="course-table">
    <thead>
        <tr>
            <th>Course ID</th>
            <th>Title</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($courses as $course): ?>
        <tr>
            <td><?= htmlspecialchars($course['id']); ?></td>
            <td><?= htmlspecialchars($course['title']); ?></td>
            <td><?= htmlspecialchars($course['description']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p style="margin-left:20px;">You are not enrolled in any courses yet.</p>
<?php endif; ?>

</body>
</html>
