<?php
session_start();
require_once "../LOGIN/config.php";

if (!isset($_SESSION['professor_id'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

$professor_id = $_SESSION['professor_id'];
$today = date("Y-m-d");

// Fetch sessions of this professor
$sessions = $conn->prepare("SELECT id, course_name, session_group FROM sessions WHERE professor_id=?");
$sessions->bind_param("i", $professor_id);
$sessions->execute();
$sessions = $sessions->get_result();

// If session selected
$attendance_data = [];
if (isset($_GET['session_id'])) {
    $session_id = intval($_GET['session_id']);

    $stmt = $conn->prepare("
        SELECT u.id, u.name, a.status, a.participation
        FROM attendance a 
        INNER JOIN users u ON a.student_id = u.id
        WHERE a.session_id = ? AND a.date = ?
    ");
    $stmt->bind_param("is", $session_id, $today);
    $stmt->execute();
    $attendance_data = $stmt->get_result();

    // Save attendance to JSON
    if ($attendance_data->num_rows > 0) {
        $json_array = [];
        while ($row = $attendance_data->fetch_assoc()) {
            $json_array[] = [
                'student_id' => $row['id'],
                'name' => $row['name'],
                'status' => $row['status'],
                'participation' => $row['participation']
            ];
        }

        $json_filename = "attendance_{$today}_session{$session_id}.json";
        file_put_contents($json_filename, json_encode($json_array, JSON_PRETTY_PRINT));
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Today's Attendance</title>
    <link href="../header/header.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; font-family: Arial, sans-serif; }
        h2 { text-align: center; margin-top: 25px; color: #333; font-size: 28px; }
        form { background: white; width: 60%; margin: 20px auto; padding: 20px; border-radius: 10px; box-shadow: 0 3px 8px rgba(0,0,0,0.12); text-align: center; }
        label { font-size: 16px; margin-right: 10px; font-weight: 600; }
        select { padding: 10px 14px; font-size: 15px; border: 1px solid #ccc; border-radius: 8px; outline: none; margin-right: 15px; transition: 0.2s; }
        select:focus { border-color: #007bff; box-shadow: 0 0 4px rgba(0,123,255,0.3); }
        button { padding: 10px 18px; background: #007bff; border: none; color: white; font-size: 15px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: 0.25s; }
        button:hover { background: #005ec4; transform: scale(1.05); }
        table { width: 80%; margin: 30px auto; border-collapse: collapse; background: white; box-shadow: 0 3px 10px rgba(0,0,0,0.15); border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px 15px; text-align: center; }
        th { background-color: #007bff; color: white; font-size: 16px; }
        td { font-size: 15px; border-bottom: 1px solid #e2e6ef; }
        tr:last-child td { border-bottom: none; }
        tr:nth-child(even) td { background-color: #f9fbff; }
        tr:hover td { background-color: #e8f0fe; transition: 0.3s; }
        @media (max-width: 768px) { form, table { width: 95%; } th, td { font-size: 14px; padding: 8px; } button, select { width: 100%; margin-top: 10px; } }
    </style>
</head>
<body>
<?php include '../header/header.php'; ?>

<h2>Today's Attendance (<?= $today ?>)</h2>

<form method="GET">
    <label>Select Session:</label>
    <select name="session_id" required>
        <option value="">-- choose session --</option>
        <?php while ($s = $sessions->fetch_assoc()): ?>
            <option value="<?= $s['id'] ?>"><?= $s['course_name'] ?> (Group <?= $s['session_group'] ?>)</option>
        <?php endwhile; ?>
    </select>
    <button type="submit">View</button>
</form>

<?php if (!empty($attendance_data)): ?>
<table>
    <thead>
        <tr>
            <th>Student</th>
            <th>Status</th>
            <th>Participation</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        // Reset pointer to start since we already fetched rows for JSON
        $attendance_data->data_seek(0);
        while ($row = $attendance_data->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td><?= ucfirst($row['participation']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<h3 style="text-align:center;color:green;">Attendance saved to JSON file: <strong><?= $json_filename ?></strong></h3>
<?php elseif(isset($_GET['session_id'])): ?>
<h3 style="text-align:center;color:red;">No attendance taken today yet.</h3>
<?php endif; ?>

</body>
</html>
