<?php
session_start();
require_once "../LOGIN/config.php";
include "../header/header.php";

if (!isset($_SESSION['administrator_id'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

$professors = $conn->query("SELECT id, name FROM users WHERE role='professor'");

$groups_result = $conn->query("SELECT DISTINCT user_group FROM users WHERE role='student'");

if (isset($_POST['add_session'])) {
    $course_name = trim($_POST['course_name']);
    $session_type = $_POST['session_type'];
    $day_of_week = $_POST['day_of_week']; 
    $time_input = $_POST['time']; 
    $professor_id = $_POST['professor_id'];
    $session_group = $_POST['session_group'] ?? '';

    $datetime = date("Y-m-d H:i:s", strtotime("next $day_of_week $time_input"));

    $stmt = $conn->prepare("INSERT INTO sessions (course_name, session_type, time, professor_id, session_group) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $course_name, $session_type, $datetime, $professor_id, $session_group);

    if ($stmt->execute()) {
        $message = "Session added successfully!";
    } else {
        $message = "Error adding session.";
    }
}

$sessions = $conn->query("
    SELECT s.*, u.name AS professor_name
    FROM sessions s
    LEFT JOIN users u ON s.professor_id = u.id
    ORDER BY s.id DESC
");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Add Session</title>
<style>
body { font-family: Arial, sans-serif; background: #eef2f7; margin:0; padding:0; }
.page-container { width: 90%; max-width: 1000px; margin: 40px auto; }
.session-form { background: white; padding: 25px; border-radius: 12px; max-width: 500px; margin: 0 auto 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.session-form h2 { text-align:center; margin-bottom:20px; color:#34495e; }
input, select { width:100%; padding:12px; margin-bottom:15px; border:1px solid #ccc; border-radius:6px; font-size:15px; transition:0.2s; }
input:focus, select:focus { border-color:#3498db; outline:none; box-shadow:0 0 5px rgba(52,152,219,0.4); }
button { width:100%; padding:12px; background:#3498db; border:none; color:white; border-radius:6px; font-size:16px; cursor:pointer; transition:0.2s; }
button:hover { background:#217dbb; }
label { font-weight:bold; margin-bottom:5px; display:block; color:#555; }
.sessions-table { width: 100%; border-collapse: collapse; margin-top: 30px; background:white; border-radius:10px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1); table-layout: fixed; }
.sessions-table th { text-align:center; background:#3498db; color:white; padding:12px; font-size:16px; }
.sessions-table td { padding:12px; text-align:center; border-bottom:1px solid #eee; font-size:15px; }
.sessions-table tr:hover { background:#f5faff; }
.message { text-align:center; font-weight:bold; color:green; margin-bottom:20px; font-size:18px; }
</style>
</head>
<body>

<div class="page-container">

    <form method="POST" class="session-form">
        <h2>Add New Session</h2>

        <input type="text" name="course_name" placeholder="Course Name" required>

        <label>Session Type:</label>
        <select name="session_type" required>
            <option value="">-- Select Type --</option>
            <option value="CM">CM</option>
            <option value="TD">TD</option>
            <option value="TP">TP</option>
        </select>

        <label>Day of Week:</label>
        <select name="day_of_week" required>
            <option value="">-- Select Day --</option>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
            <option value="Saturday">Saturday</option>
            <option value="Sunday">Sunday</option>
        </select>

        <label>Time:</label>
        <input type="time" name="time" required>

        <label>Assign Professor:</label>
        <select name="professor_id" required>
            <option value="">-- Select Professor --</option>
            <?php while ($prof = $professors->fetch_assoc()): ?>
                <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Assign Group:</label>
        <select name="session_group" required>
            <option value="">-- Select Group --</option>
            <?php while ($g = $groups_result->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($g['user_group']) ?>"><?= htmlspecialchars($g['user_group']) ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit" name="add_session">Add Session</button>
    </form>

    <?php if (isset($message)) : ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <h2>All Sessions</h2>
    <table class="sessions-table">
        <tr>
            <th>ID</th>
            <th>Course</th>
            <th>Type</th>
            <th>Professor</th>
            <th>Group</th>
            <th>Day & Time</th>
        </tr>
        <?php while ($row = $sessions->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['course_name']) ?></td>
            <td><?= htmlspecialchars($row['session_type']) ?></td>
            <td><?= htmlspecialchars($row['professor_name'] ? $row['professor_name'] : "Not Assigned") ?></td>
            <td><?= htmlspecialchars($row['session_group']) ?></td>
            <td><?= date("l H:i", strtotime($row['time'])) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

</body>
</html>
