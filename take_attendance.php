<?php
session_start();
require_once "LOGIN/config.php";

if(!isset($_SESSION['professor_id'])){
    header("Location: LOGIN/login.php");
    exit();
}

$professor_id = $_SESSION['professor_id'];

if(!isset($_GET['session_id'])){
    echo "<h3 style='text-align:center;color:red;'>No session selected!</h3>";
    exit;
}

$session_id = intval($_GET['session_id']);

$stmt = $conn->prepare("SELECT * FROM sessions WHERE id=? AND professor_id=?");
$stmt->bind_param("ii", $session_id, $professor_id);
$stmt->execute();
$session_result = $stmt->get_result();
if($session_result->num_rows == 0){
    echo "<h3 style='text-align:center;color:red;'>This session does not belong to you!</h3>";
    exit;
}

$stmt = $conn->prepare("
    SELECT u.id, u.name
    FROM users u
    JOIN enrollments e ON e.student_id = u.id
    WHERE e.session_id = ?
");
$stmt->bind_param("i", $session_id);
$stmt->execute();
$students = $stmt->get_result();

$message = '';
$today = date("Y-m-d");
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    foreach($students as $student){
        $id = $student['id'];
        $status = isset($_POST["status_$id"]) ? $_POST["status_$id"] : 'absent';

        $stmt = $conn->prepare("
            INSERT INTO attendance (student_id, session_id, status, date)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE status=VALUES(status)
        ");
        $stmt->bind_param("iiss", $id, $session_id, $status, $today);
        $stmt->execute();
    }

    $message = "<div id='attendance-msg' style='text-align:center;color:green;margin:15px 0;'>Attendance saved successfully!</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Take Attendance</title>
    <link href="/header/header.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: Arial, sans-serif; margin: 0; padding: 0; }
        table { border-collapse: collapse; width: 80%; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: center; border: 1px solid #ccc; }
        th { background: #007bff; color: #fff; font-size: 16px; }
        tr:hover { background: #eaf6ea; }
        button { padding: 10px 20px; margin: 20px auto; display: block; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 5px; font-size: 16px; transition: 0.3s; }
        button:hover { background: #0056b3; transform: scale(1.05); }
    </style>
</head>
<body>

<?php include 'header/header.php'; ?>

<?= $message; ?>

<h2 style="text-align:center; margin: 20px auto;">Take Attendance for Session ID: <?= $session_id ?></h2>

<form method="POST">
    <table>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Status</th>
        </tr>
        <?php foreach($students as $student): ?>
        <tr>
            <td><?= $student['id'] ?></td>
            <td><?= htmlspecialchars($student['name']) ?></td>
            <td>
                <label><input type="radio" name="status_<?= $student['id'] ?>" value="present" required> Present</label>
                <label><input type="radio" name="status_<?= $student['id'] ?>" value="absent"> Absent</label>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <button type="submit">Save Attendance</button>
</form>

<script>
    const msg = document.getElementById('attendance-msg');
    if(msg){
        setTimeout(() => {
            msg.style.display = 'none';
        }, 3000);
    }
</script>

</body>
</html>
