<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: LOGIN/login.php");
    exit();
}

require_once "LOGIN/config.php";

$student_id = $_SESSION['student_id'];

$sql = "
    SELECT 
        s.id AS session_id,
        s.course_name,
        s.session_type,
        s.time,
        u.name AS professor_name
    FROM enrollments e
    INNER JOIN sessions s ON e.session_id = s.id
    LEFT JOIN users u ON s.professor_id = u.id
    WHERE e.student_id = ?
    ORDER BY s.time
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$sessions = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<link href="/header/header.css" rel="stylesheet">
<style>
table {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
}
th, td {
    border: 1px solid #ccc;
    padding: 10px;
}
th {
    background: #f2f2f2;
}
 .presence
 {
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
</style>
</head>
<body>

<?php include 'header/header.php'; ?>

<h1 style="margin-left:20px;">Welcome, <?= htmlspecialchars($_SESSION['name']); ?></h1>
<h2 style="margin-left:20px;">Your Sessions:</h2>

<?php if (!empty($sessions)): ?>
<table>
    <thead>
        <tr>
            <th>Session Name</th>
            <th>Type</th>
            <th>Time</th>
            <th>Professor</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sessions as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['course_name']); ?></td>
            <td><?= htmlspecialchars($s['session_type']); ?></td>
            <td><?= htmlspecialchars($s['time']); ?></td>
            <td><?= htmlspecialchars($s['professor_name']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p style="margin-left:20px;">You have no assigned sessions yet.</p>
<?php endif; ?>
<div class="presence">
<h2>See presence</h2>
   <a href="see_preseence.php" class="btn">Go</a>
</div>
</body>
</html>
