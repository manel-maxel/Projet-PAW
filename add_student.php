<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "db_connect.php";

$student_id = $name = $group = '';
$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $group = trim($_POST['group'] ?? '');
    
    if (empty($student_id)) {
        $errors['student_id'] = 'Student ID is required.';
    } elseif (!is_numeric($student_id)) {
        $errors['student_id'] = 'Student ID must be a number.';
    }
    
    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $errors['name'] = 'Name can only contain letters and spaces.';
    }
    
    if (empty($group)) {
        $errors['group'] = 'Group is required.';
    }
    
    if (empty($errors)) {
        $conn = connectDB();
        
        if (!$conn) {
            $errors['general'] = 'Database connection failed.';
        } else {
            try {
                $check_stmt = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
                $check_stmt->execute([$student_id]);
                
                if ($check_stmt->fetch()) {
                    $errors['student_id'] = 'Student ID already exists.';
                } else {
                    $insert_stmt = $conn->prepare("INSERT INTO students (student_id, name, group_name) VALUES (?, ?, ?)");
                    $result = $insert_stmt->execute([$student_id, $name, $group]);
                    
                    if ($result) {
                        $success_message = 'Student added successfully to database!';
                        $student_id = $name = $group = '';
                    } else {
                        $errors['general'] = 'Error saving student data to database.';
                    }
                }
            } catch (PDOException $e) {
                $errors['general'] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

$students = [];
$conn = connectDB();
if ($conn) {
    try {
        $stmt = $conn->query("SELECT student_id, name, group_name FROM students ORDER BY student_id");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #495057;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus {
            outline: none;
            border-color: #007bff;
        }
        .error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }
        .success {
            color: #28a745;
            font-size: 16px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            text-align: center;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
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
    <div class="container">
        <h1>Add New Student</h1>
        
        <?php if (!empty($success_message)): ?>
            <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <form method="post" action="add_student.php">
            <div class="form-group">
                <label for="student_id">Student ID:</label>
                <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>" required>
                <?php if (isset($errors['student_id'])): ?>
                    <div class="error"><?php echo htmlspecialchars($errors['student_id']); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                <?php if (isset($errors['name'])): ?>
                    <div class="error"><?php echo htmlspecialchars($errors['name']); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="group">Group:</label>
                <input type="text" id="group" name="group" value="<?php echo htmlspecialchars($group); ?>" required>
                <?php if (isset($errors['group'])): ?>
                    <div class="error"><?php echo htmlspecialchars($errors['group']); ?></div>
                <?php endif; ?>
            </div>
            
            <?php if (isset($errors['general'])): ?>
                <div class="error"><?php echo htmlspecialchars($errors['general']); ?></div>
            <?php endif; ?>
            
            <button type="submit">Add Student</button>
        </form>
        
        <a href="attendenci.html" class="back-link">← Back to Attendance System</a>
        
        <?php if (count($students) > 0): ?>
            <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                <h3>Existing Students (<?php echo count($students); ?>)</h3>
                <?php foreach ($students as $student): ?>
                    <p>ID: <?php echo htmlspecialchars($student['student_id']); ?> - 
                       <?php echo htmlspecialchars($student['name']); ?> (<?php echo htmlspecialchars($student['group_name']); ?>)</p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>