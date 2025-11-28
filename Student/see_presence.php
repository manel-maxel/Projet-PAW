<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

require_once "../LOGIN/config.php";

$student_id = $_SESSION['student_id'];

// Get student's group and name
$stmt = $conn->prepare("SELECT user_group, name FROM users WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$student_group = $user['user_group'];
$student_name = $user['name'];

// Fetch attendance + justifications for this student
$sql = "
    SELECT 
        s.id AS session_id,
        s.course_name,
        s.session_type,
        s.time,
        a.status AS attendance_status,
        j.status AS justification_status,
        j.justification_text
    FROM attendance a
    INNER JOIN sessions s ON a.session_id = s.id
    LEFT JOIN justifications j ON j.session_id = s.id AND j.student_id = a.student_id
    WHERE a.student_id = ?
      AND s.session_group = ?
    ORDER BY s.time DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $student_id, $student_group);
$stmt->execute();
$result = $stmt->get_result();
$attendances = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Attendance</title>
<link href="../header/header.css" rel="stylesheet">
<style>
table { width: 90%; margin: 20px auto; border-collapse: collapse; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
th { background: #007bff; color: white; }
textarea { width: 90%; }
button { padding: 5px 10px; margin-top: 5px; }
form { margin: 0; }
.approved { color: green; font-weight: bold; }
.rejected { color: red; font-style: italic; }
.pending { color: orange; font-weight: bold; }
</style>
</head>
<body>

<?php include '../header/header.php'; ?>

<h1 style="text-align:center;">My Attendance (Group: <?= htmlspecialchars($student_group) ?>)</h1>

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
                        <span class="<?= $row['justification_status'] ?>">
                            <?= ucfirst($row['justification_status']) ?>: <?= htmlspecialchars($row['justification_text']) ?>
                        </span>
                    <?php else: ?>
                        <form method="POST" action="submit_justification.php">
                            <input type="hidden" name="session_id" value="<?= $row['session_id'] ?>">
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
<p style="text-align:center;">No attendance records found for your group.</p>
<?php endif; ?>

</body>
</html>
