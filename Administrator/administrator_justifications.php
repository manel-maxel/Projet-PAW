<?php
session_start();
require_once "../LOGIN/config.php";

if (!isset($_SESSION['administrator_id'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $justification_id = intval($_POST['justification_id']);
    $action = $_POST['action']; 

    if ($justification_id > 0 && in_array($action, ['approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE justifications SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $justification_id);
        $stmt->execute();
        $_SESSION['message'] = "Justification has been $action" . "d successfully!";
        header("Location: administrator_justifications.php");
        exit();
    }
}

// Fetch pending justifications
$result = $conn->query("
    SELECT j.id, j.justification_text, j.status, j.session_id, s.course_name, u.name AS student_name
    FROM justifications j
    JOIN users u ON j.student_id = u.id AND u.role = 'student'
    JOIN sessions s ON j.session_id = s.id
    WHERE j.status = 'pending'
    ORDER BY j.created_at DESC
");


?>

<!DOCTYPE html>
<html>
<head>
    <title>Pending Justifications</title>
    <link href="../header/header.css" rel="stylesheet">
    <style>
        body {
          
            background-color: #f9f9f9;
        }

        h2 {
            color: #333;
            margin:20px 0px ;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #4c81afff;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            text-transform: capitalize;
        }


        .approved {
            background-color: #5cb85c;
        }

        .rejected {
            background-color: #d9534f;
        }

        button {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            font-weight: bold;
            transition: 0.2s;
        }

        button[value="approved"] {
            background-color: #5cb85c;
        }

        button[value="approved"]:hover {
            background-color: #449d44;
        }

        button[value="rejected"] {
            background-color: #d9534f;
        }

        button[value="rejected"]:hover {
            background-color: #c9302c;
        }

        p.message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border-radius: 5px;
            width: fit-content;
        }

        @media screen and (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            tr { margin-bottom: 15px; }
            th {
                background-color: transparent;
                color: #555;
            }
            td {
                border: none;
                position: relative;
                padding-left: 50%;
            }
            td:before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                font-weight: bold;
            }
        }
    </style>
</head>
<body>
  <?php include '../header/header.php'; ?>
    <h2>Pending Justifications</h2>

    <?php if(isset($_SESSION['message'])): ?>
        <p style="color: green;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Course / Session</th>
                <th>Justification</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['justification_text']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="justification_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="approved">Approve</button>
                            <button type="submit" name="action" value="rejected">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php if($result->num_rows === 0): ?>
                <tr><td colspan="4">No pending justifications.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
