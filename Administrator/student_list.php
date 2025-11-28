<?php
session_start();
if (!isset($_SESSION['administrator_id'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

require_once "../LOGIN/config.php";

// Add Student
if (isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $group = $_POST['group'] ?? '';

    $conn->query("INSERT INTO users (name, email, password, role, user_group) VALUES ('$name','$email','$password','student','$group')");
    header("Location: student_list.php");
    exit();
}

// Delete Student
if (isset($_GET['delete_student'])) {
    $id = $_GET['delete_student'];
    $conn->query("DELETE FROM users WHERE id='$id' AND role='student'");
    header("Location: student_list.php");
    exit();
}

// Export Students (CSV)
if (isset($_POST['export_students'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="students.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'Email', 'Group']);
    $result = $conn->query("SELECT name, email, user_group FROM users WHERE role='student'");
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

// Import Students (CSV only)
if (isset($_POST['import_students']) && isset($_FILES['import_file'])) {
    $file = fopen($_FILES['import_file']['tmp_name'], 'r');
    fgetcsv($file); 
    while (($data = fgetcsv($file)) !== FALSE) {
        $name = $data[0];
        $email = $data[1];
        $password = password_hash($data[2], PASSWORD_DEFAULT);
        $group = $data[3] ?? '';
        $conn->query("INSERT INTO users (name,email,password,role,user_group) VALUES ('$name','$email','$password','student','$group')");
    }
    fclose($file);
    header("Location: student_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student List Management</title>
<link href="../header/header.css" rel="stylesheet">
<style>
body { 
  background: #f5f5f5; 
}
h1 { text-align:center; margin-bottom:30px; margin-top:20px; }
h2 { margin-top:40px; }
button {
    background: #007BFF;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
}
button:hover { background: #0056b3; transform: scale(1.05); }
input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
    padding: 8px; margin: 5px; font-size: 16px; border-radius: 6px; border: 1px solid #ccc; width: 250px;
}
form { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
table { width: 100%; border-collapse: collapse; background:white; }
th, td { padding: 10px; border: 1px solid #ddd; text-align:center; }
th { background: #007BFF; color:white; }
a { color: red; text-decoration:none; }
a:hover { text-decoration: underline; }
</style>
</head>
<body>

<?php include '../header/header.php'; ?>

<h1>Student List Management</h1>

<h2>Add New Student</h2>
<form method="post">
    <input type="text" name="name" placeholder="Student Name" required>
    <input type="email" name="email" placeholder="Student Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="text" name="group" placeholder="Group" required>
    <button type="submit" name="add_student">Add Student</button>
</form>

<h2>Import / Export Students</h2>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="import_file" accept=".csv" required>
    <button type="submit" name="import_students">Import Students</button>
</form>

<form method="post">
    <button type="submit" name="export_students">Export Students</button>
</form>

<h2>Current Students</h2>
<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Group</th>
    <th>Action</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM users WHERE role='student'");
while ($student = $result->fetch_assoc()) {
    $group = $student['user_group'] ?? '';
    echo "<tr>
        <td>{$student['id']}</td>
        <td>{$student['name']}</td>
        <td>{$student['email']}</td>
        <td>{$group }</td>
        <td><a href='?delete_student={$student['id']}' onclick='return confirm(\"Delete this student?\")'>Delete</a></td>
    </tr>";
}
?>
</table>

</body>
</html>
