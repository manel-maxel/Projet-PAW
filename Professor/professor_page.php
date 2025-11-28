<?php
session_start();

if (!isset($_SESSION['professor_id'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Professor Page</title>
    <link href="../header/header.css" rel="stylesheet">
    <style>
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
<?php include '../header/header.php'; ?>
<h1>Professor Dashboard</h1>

<div class="dashboard-container">

    <div class="card">
        <h2>Show </br>Session</h2>
        <a href="professor_see_session.php" class="btn">Go</a>
    </div>

    <div class="card">
        <h2>Mark Attendance</h2>
        <a href="select_session.php"  class="btn">Go</a>
    </div>

    <div class="card">
        <h2>Attendance Summary</h2>
        <a href="attendance_summary.php" class="btn">Go</a>
    </div>
   
    <div class="card">
        <h2>View Today's Attendance</h2>
        <a href="attendance_today.php" class="btn">Go</a>
    </div>

</body>
</html>
