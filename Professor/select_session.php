<?php
session_start();
require_once "../LOGIN/config.php";

if(!isset($_SESSION['professor_id'])){
    header("Location: ../LOGIN/login.php");
    exit();
}

$professor_id = $_SESSION['professor_id'];

$stmt = $conn->prepare("SELECT * FROM sessions WHERE professor_id=? ORDER BY time DESC");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$sessions = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Session</title>
    <link href="../header/header.css" rel="stylesheet">
    <style>
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
        th { background: #007bff; color: #fff; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <?php include '../header/header.php'; ?>
    <h2 style="text-align:center; margin: 20px auto;">Select a Session to Mark Attendance</h2>
    <table>
        <tr>
            <th>Group</th>
            <th>Course Name</th>
            <th>Type</th>
            <th>Date & Time</th>
            <th>Action</th>
        </tr>
        <?php while($session = $sessions->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($session['session_group']) ?></td>
            <td><?= htmlspecialchars($session['course_name']) ?></td>
            <td><?= $session['session_type'] ?></td>
            <td><?= $session['time'] ?></td>
            <td><a href="take_attendance.php?session_id=<?= $session['id'] ?>">Mark Attendance</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
