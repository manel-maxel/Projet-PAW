<?php
session_start();
require_once "../LOGIN/config.php";

if (!isset($_SESSION['administrator_id'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}
//number of student
$result = $conn->query("SELECT COUNT(*) AS total_students FROM users WHERE role='student'");
$total_students = $result->fetch_assoc()['total_students'];
//number of teacher
$result = $conn->query("SELECT COUNT(*) AS total_professors FROM users WHERE role='professor'");
$total_professors = $result->fetch_assoc()['total_professors'];

// Get sessions count per course
$sessions_data = [];
$result = $conn->query("
    SELECT course_name, COUNT(*) AS sessions_count
    FROM sessions
    GROUP BY course_name
");
while ($row = $result->fetch_assoc()) {
    $sessions_data[] = $row;
}

// Get attendance percentage per course
$attendance_data = [];
$result = $conn->query("
    SELECT s.course_name,
           SUM(a.status='present') AS present_count,
           COUNT(a.id) AS total_count
    FROM sessions s
    LEFT JOIN attendance a ON a.session_id = s.id
    GROUP BY s.course_name
");
while ($row = $result->fetch_assoc()) {
    $attendance_data[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Administrator Statistics</title>
<link href="../header/header.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
h1 { text-align:center; margin-top:30px; }
.chart-container { width: 80%; margin: 40px auto; }
.stats-box { text-align:center; margin: 20px; font-size:18px; }
</style>
</head>
<body>

<?php include 'header/header.php'; ?>

<h1>Administrator Statistics</h1>

<div class="stats-box">
    <p>Total Students: <?= $total_students ?></p>
    <p>Total Professors: <?= $total_professors ?></p>
</div>

<div class="chart-container">
    <h2>Sessions per Course</h2>
    <canvas id="sessionsChart"></canvas>
</div>

<div class="chart-container" style="width: 50%; margin: 20px auto;">
    <h2>Attendance Percentage per Course</h2>
   <canvas id="attendanceChart" width="150" height="150"></canvas>
</div>

<script>
  //Sessions per Course
const sessionsCtx = document.getElementById('sessionsChart').getContext('2d');
const sessionsChart = new Chart(sessionsCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($sessions_data, 'course_name')) ?>,
        datasets: [{
            label: 'Number of Sessions',
            data: <?= json_encode(array_column($sessions_data, 'sessions_count')) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// %attendance
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(attendanceCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($attendance_data, 'course_name')) ?>,
        datasets: [{
            label: 'Attendance Percentage',
            data: <?= json_encode(array_map(function($row) {
                return $row['total_count'] ? round(($row['present_count'] / $row['total_count']) * 100, 2) : 0;
            }, $attendance_data)) ?>,
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)',
                'rgba(255, 99, 132, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true
    }
});
</script>

</body>
</html>
