<?php

$studentsFile = "students.json";

if (!file_exists($studentsFile)) {
    die("students.json not found");
}
$students = json_decode(file_get_contents($studentsFile), true);
$today = date("Y-m-d");
$attendanceFile = "attendance_" . $today . ".json";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (file_exists($attendanceFile)) {
        echo "<h3 style='color:red; text-align:center;'>Attendance for today has already been taken.</h3>";
        exit;
    }

    $attendanceData = [];
    foreach ($students as $student) {
        $id = $student["student_id"];
        $status = isset($_POST["status_$id"]) ? $_POST["status_$id"] : "absent";

        $attendanceData[] = [
            "student_id" => $id,
            "status" => $status
        ];
    }
    file_put_contents($attendanceFile, json_encode($attendanceData, JSON_PRETTY_PRINT));

    echo "<h3 style='color:green; text-align:center;'>Attendance saved successfully!</h3>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Attendance</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
            color: #333;
        }

        table {
            margin: 30px auto;
            border-collapse: collapse;
            width: 80%;
            background: #fff;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: center;
        }

        th {
            background: #007bff;
            color: white;
            font-size: 18px;
        }

        tr:nth-child(even) {
            background: #f7f7f7;
        }

        tr:hover {
            background: #eaf6ea;
        }

        button {
            display: block;
            margin: 20px auto;
            padding: 12px 25px;
            background-color: #007bff;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #007bff;
            transform: scale(1.05);
        }

        label {
            margin: 0 10px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>

</head>
<body>

<h2>Take Attendance</h2>

<?php

if (file_exists($attendanceFile)) {
    echo "<h3 style='color:red; text-align:center;'>Attendance for today has already been taken.</h3>";
    exit;
}
?>
<form method="POST">
    <table>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Status</th>
        </tr>

        <?php foreach ($students as $student): ?>
            <tr>
                <td><?= $student["student_id"] ?></td>
                <td><?= $student["name"] ?></td>
                <td>
                    <label>
                        <input type="radio" name="status_<?= $student["student_id"] ?>" value="present" required>
                        Present
                    </label>

                    <label>
                        <input type="radio" name="status_<?= $student["student_id"] ?>" value="absent">
                        Absent
                    </label>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <button type="submit">Save Attendance</button>
</form>
   <a href="attendenci.html" class="back-link">← Back to Attendance System</a>
</body>
</html>
