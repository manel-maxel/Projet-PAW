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
<link href="/header/header.css" rel="stylesheet">
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f5f5;
        margin: 0;
        padding: 0;
    }

    h1 {
        text-align: center;
        margin-top: 40px;
    }

    .dashboard-container {
        display: flex;
        justify-content: center;   
        gap: 50px;                
        margin-top: 40px;
    }

    .card {
        width: 220px;
        background: white;
        padding: 30px;
        text-align: center;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .btn {
        display: inline-block;
        background: #007BFF;
        color: white;
        padding: 10px 25px;
        border-radius: 6px;
        text-decoration: none;
        margin-top: 10px;
        font-size: 18px;
    }

    .btn:hover {
        background: #0056b3;
    }
</style>
</head>

<body>
<?php include 'header/header.php'; ?>
<h1>Administrator Dashboard</h1>

<div class="dashboard-container">

    <div class="card">
        <h2>Add </br> Course</h2>
        <a href="administrator_add_course.php" class="btn">Go</a>
    </div>

    <div class="card">
        <h2>Statistics Page</h2>
        <a href="administrator_statistics_page.php" class="btn">Go</a>
    </div>

    <div class="card">
        <h2>Student </br> List</h2>
        <a href="student_list.php" class="btn">Go</a>
    </div>

</div>

</body>
</html>
