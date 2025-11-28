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
        a.status AS attendance_status,
        j.status AS justification_status,
        j.justification_text
    FROM enrollments e
    INNER JOIN sessions s ON e.session_id = s.id
    LEFT JOIN attendance a ON a.session_id = s.id AND a.student_id = e.student_id
    LEFT JOIN justifications j ON j.session_id = s.id AND j.student_id = e.student_id
    WHERE e.student_id = ?
    ORDER BY s.time DESC
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$attendances = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Attendance</title>
<link href="/header/header.css" rel="stylesheet">
<style>
table { width: 90%; margin: 20px auto; border-collapse: collapse; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
th { background: #007bff; color: white; }
textarea { width: 90%; }
button { padding: 5px 10px; }
form { margin: 0; }
</style>
</head>
<body>

<?php include 'header/header.php'; ?>

<h1 style="text-align:center;">My Attendance</h1>

<?php if(!empty($attendances)): ?>
<table>
    <thead>
        <tr>
            <th>Course</th>
            <th>Type</th>
            <th>Date & Time</th>
            <th>Status</th>
            <th>Justification</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($attendances as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['course_name']); ?></td>
            <td><?= htmlspecialchars($row['session_type']); ?></td>
            <td><?= $row['time']; ?></td>
            <td><?= $row['attendance_status'] ?? 'Not marked'; ?></td>
            <td>
                <?php if($row['attendance_status'] === 'absent'): ?>
                    <?php if($row['justification_status']): ?>
                        <?= ucfirst($row['justification_status']) ?>: <?= htmlspecialchars($row['justification_text']) ?>
                    <?php else: ?>
                        <form method="POST" action="submit_justification.php">
                            <input type="hidden" name="session_id" value="<?= $row['session_id'] ?? 0 ?>">
                            <textarea name="justification_text" placeholder="Enter justification" required></textarea><br>
                            <button type="submit">Submit</button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p style="text-align:center;">No attendance records found.</p>
<?php endif; ?>

</body>
</html>
