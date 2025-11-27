<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: LOGIN/login.php");
    exit();
}

require_once "LOGIN/config.php";

$student_id = $_SESSION['student_id'];


$sql = "SELECT s.course_name, s.session_type, s.time, u.name AS professor_name
        FROM enrollments e
        JOIN sessions s ON s.course_name = e.course_name
        JOIN users u ON s.professor_id = u.id
        WHERE e.student_id = ?
        ORDER BY s.course_name, s.time";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$sessions = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Student Home</title>
<link href="/header/header.css" rel="stylesheet">
<style>
body { font-family: Arial; }
.container { width: 90%; margin: 20px auto; }
.session-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
.session-table th, .session-table td { padding: 12px; border: 1px solid #ddd; }
.session-table th { background: #f0f0f0; text-align: left; }
.session-table tr:nth-child(even) { background: #fafafa; }
.empty { background: #fff3cd; padding: 15px; border: 1px solid #ffeeba; border-radius:5px; }
</style>
</head>
<body>
<?php include 'header/header.php'; ?>
<div class="container">
<h1>Bienvenue, <?= htmlspecialchars($_SESSION['name']); ?></h1>
<h2>Vos Séances (CM / TD / TP)</h2>

<?php if (!empty($sessions)): ?>
<table class="session-table">
<thead>
<tr><th>Professeur</th><th>Course</th><th>Type</th><th>Horaire</th></tr>
</thead>
<tbody>
<?php foreach ($sessions as $s): ?>
<tr>
<td><?= htmlspecialchars($s['professor_name']); ?></td>
<td><?= htmlspecialchars($s['course_name']); ?></td>
<td><?= htmlspecialchars($s['session_type']); ?></td>
<td><?= htmlspecialchars($s['time']); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<div class="empty">Vous n'êtes inscrit à aucune séance.</div>
<?php endif; ?>
</div>
</body>
</html>
