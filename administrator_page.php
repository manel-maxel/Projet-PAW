<?php
session_start();
if (!isset($_SESSION['administrator_id'])) {
    header("Location: LOGIN/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Administrator Dashboard</title>
<link href="header/header.css" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; padding: 20px; background: #f9f9f9; }
h1 { margin-bottom: 20px; }
.dashboard { display: flex; gap: 20px; }
.card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; width: 200px; }
.card a { text-decoration: none; color: white; background: #3498db; padding: 10px 15px; display: inline-block; border-radius: 5px; margin-top: 10px; }
</style>
</head>
<body>

<?php include "header/header.php"; ?>

<h1>Welcome, <?= htmlspecialchars($_SESSION['name']); ?>!</h1>
<h2>Administrator Dashboard</h2>

<div class="dashboard">
    <div class="card">
        <h3>Add Course</h3>
        <a href="administrator_add_course.php">Go</a>
    </div>
    <div class="card">
        <h3>View Courses</h3>
        <a href="administrator_view_courses.php">Go</a>
    </div>
    <div class="card">
        <h3>Enroll Students</h3>
        <a href="administrator_enroll_students.php">Go</a>
    </div>
</div>

</body>
</html>
