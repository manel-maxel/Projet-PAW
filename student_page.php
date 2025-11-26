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

</head>
<body>

<?php include 'header/header.php'; ?>

<h1>Welcome, <?= htmlspecialchars($_SESSION['name']); ?></h1>
<h2>Your Enrolled Courses:</h2>

<ul class="course-list">
<?php if (!empty($courses)): ?>
    <?php foreach ($courses as $course): ?>
        <li class="course-item">
            <strong><?= htmlspecialchars($course['title']); ?></strong><br>
            <?= htmlspecialchars($course['description']); ?>
        </li>
    <?php endforeach; ?>
<?php else: ?>
    <li>You are not enrolled in any courses yet.</li>
<?php endif; ?>
</ul>

</body>
</html>
