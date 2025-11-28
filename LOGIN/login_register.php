<?php
session_start();
require_once "config.php";

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $user_group = ($role === 'student') ? trim($_POST['group']) : NULL;

    //if email already exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, user_group) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $password, $role, $user_group);

        if ($stmt->execute()) {
            $_SESSION['register_success'] = "Registration successful! You can now log in.";
        } else {
            $_SESSION['register_error'] = "Registration failed. Please try again.";
            $_SESSION['active_form'] = 'register';
        }
    }

    header("Location: login.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = trim($_POST['login_email']);
    $password = $_POST['login_password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];

            if ($user['role'] === 'student') {
                $_SESSION['student_id'] = $user['id'];
                $_SESSION['student_group'] = $user['user_group'];
                header("Location: ../Student/student_page.php");
            } elseif ($user['role'] === 'professor') {
                $_SESSION['professor_id'] = $user['id'];
                header("Location: ../Professor/professor_page.php");
            } else { 
                $_SESSION['administrator_id'] = $user['id'];
                header("Location: ../Administrator/administrator_page.php");
            }
            exit();
        }
    }

    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header("Location: login.php");
    exit();
}
?>
