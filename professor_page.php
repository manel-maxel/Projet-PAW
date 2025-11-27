<?php
session_start();
if (!isset($_SESSION['professor_id'])) {
    header("Location: LOGIN/login.php");
    exit();
}

require_once "LOGIN/config.php";

$professor_id = $_SESSION['professor_id'];

$sql = "SELECT course_name, session_type, time
        FROM sessions
        WHERE professor_id = ?
        ORDER BY course_name, time";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();
$sessions = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Professeur - Mes Séances</title>
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
<h1>Bonjour, <?= htmlspecialchars($_SESSION['name']); ?></h1>
<h2>Vos Séances (CM / TD / TP)</h2>

<?php if (!empty($sessions)): ?>
<table class="session-table">
<thead>
<tr><th>Course</th><th>Type</th><th>Horaire</th></tr>
</thead>
<tbody>
<?php foreach ($sessions as $s): ?>
<tr>
<td><?= htmlspecialchars($s['course_name']); ?></td>
<td><?= htmlspecialchars($s['session_type']); ?></td>
<td><?= htmlspecialchars($s['time']); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<div class="empty">Aucune séance programmée pour vos cours.</div>
<?php endif; ?>
</div>
</body>
</html>
