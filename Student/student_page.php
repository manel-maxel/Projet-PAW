<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

require_once "../LOGIN/config.php";

$student_id = $_SESSION['student_id'];

// Get student's group
$stmt = $conn->prepare("SELECT user_group, name FROM users WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$student_group = $student['user_group'] ?? '';
$student_name = $student['name'] ?? 'Student';
$stmt->close();

// Get all sessions for student's group
$sql = "
    SELECT 
        s.id AS session_id,
        s.course_name,
        s.session_type,
        s.time,
        s.session_group,
        u.name AS professor_name
    FROM sessions s
    LEFT JOIN users u ON s.professor_id = u.id
    WHERE FIND_IN_SET(?, s.session_group) > 0
    ORDER BY s.time
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_group);
$stmt->execute();
$result = $stmt->get_result();
$sessions = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<link href="../header/header.css" rel="stylesheet">
<style>
body { font-family: Arial, sans-serif; background: #f4f6f8; }
h1, h2 { margin-left: 20px; }
table {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: center;
}
th {
    background: #3498db;
    color: white;
}
tr:hover { background: #f5faff; }
.presence {
    width: 220px;
    background: white;
    padding: 30px;
    text-align: center;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    margin: 20px auto;
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
</style>
</head>
<body>

<?php include '../header/header.php'; ?>

<h1>Welcome, <?= htmlspecialchars($student_name); ?></h1>
<h2>Your Sessions (Group: <?= htmlspecialchars($student_group); ?>)</h2>

<?php if (!empty($sessions)): ?>
<table>
    <thead>
        <tr>
            <th>Session Name</th>
            <th>Type</th>
            <th>Time</th>
            <th>Professor</th>
            <th>Group</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sessions as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['course_name']); ?></td>
            <td><?= htmlspecialchars($s['session_type']); ?></td>
            <td><?= date("l, d-m-Y H:i", strtotime($s['time'])); ?></td>
            <td><?= htmlspecialchars($s['professor_name'] ?? 'Not Assigned'); ?></td>
            <td><?= htmlspecialchars($s['session_group']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p style="margin-left:20px;">You have no assigned sessions yet.</p>
<?php endif; ?>

<div class="presence">
    <h2>See Presence</h2>
    <a href="see_presence.php" class="btn">Go</a>
</div>

</body>
</html>
