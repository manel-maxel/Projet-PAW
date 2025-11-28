<?php
session_start();
require_once "LOGIN/config.php";

if (!isset($_SESSION['professor_id'])) {
    header("Location: LOGIN/login.php");
    exit();
}

$professor_id = $_SESSION['professor_id'];

$sql = "
    SELECT 
        s.id AS session_id,
        s.course_name,
        s.session_type,
        s.time,
        COUNT(a.id) AS total_marked,
        SUM(a.status='present') AS present_count,
        SUM(a.status='absent') AS absent_count
    FROM sessions s
    LEFT JOIN attendance a ON a.session_id = s.id
    WHERE s.professor_id = ?
    GROUP BY s.id
    ORDER BY s.time DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();
$sessions = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Attendance Summary</title>
<link href="/header/header.css" rel="stylesheet">
<style>
table { width: 90%; margin: 20px auto; border-collapse: collapse; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
th { background: #007bff; color: white; }
tr:hover { background: #f2f2f2; }
h1 { text-align: center; margin-top: 30px; }
</style>
</head>
<body>

<?php include 'header/header.php'; ?>

<h1>Attendance Summary</h1>

<?php if(!empty($sessions)): ?>
<table>
    <thead>
        <tr>
            <th>Course</th>
            <th>Type</th>
            <th>Day & Time</th>
            <th>Total Marked</th>
            <th>Present</th>
            <th>Absent</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($sessions as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['course_name']); ?></td>
            <td><?= htmlspecialchars($row['session_type']); ?></td>
            <td><?= date("l H:i", strtotime($row['time'])) ?></td>
            <td><?= $row['total_marked'] ?? 0 ?></td>
            <td><?= $row['present_count'] ?? 0 ?></td>
            <td><?= $row['absent_count'] ?? 0 ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p style="text-align:center;">No sessions found.</p>
<?php endif; ?>

</body>
</html>
