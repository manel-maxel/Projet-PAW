<?php
session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];
$success = $_SESSION['register_success'] ?? '';
$activeForm = $_SESSION['active_form'] ?? 'login';

session_unset();

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function showSuccess($msg) {
    return !empty($msg) ? "<p class='success-message'>$msg</p>" : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<link href="../css/stYl.css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Système de Présence - Connexion</title>
</head>
<body>
<div class="container">

    <!-- LOGIN FORM -->
    <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
        <form action="login_register.php" method="post">
            <h2>Login</h2>
            <?= showError($errors['login']); ?>
            <?= showSuccess($success); ?>
            <div class="inputgroup">
                <label for="login_email">Email Address</label>
                <input type="email" id="login_email" name="login_email" placeholder="Enter your email" required>
            </div>
            <div class="inputgroup">
                <label for="login_password">Password</label>
                <input type="password" id="login_password" name="login_password" placeholder="Enter your password" required>
            </div>
            <input type="hidden" name="login" value="1">
            <button type="submit" class="btnlgn">Login</button>
            <p>Don't have an account? <a href="#" id="show-register">Create Account</a></p>
        </form>
    </div>

    <!-- REGISTER FORM -->
    <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
        <form method="POST" action="login_register.php">
            <h2>Register</h2>
            <?= showError($errors['register']); ?>
            <div class="inputgroup">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>
            <div class="inputgroup">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="inputgroup">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="inputgroup">
                <label for="role">Role</label>
                <select name="role" id="role" required>
                    <option value="">Select Role</option>
                    <option value="student">Student</option>
                    <option value="professor">Professor</option>
                    <option value="administrator">Administrator</option>
                </select>
            </div>
            <div class="inputgroup" id="group-field" style="display: none;">
                <label for="group">Group</label>
                <input type="text" id="group" name="group" placeholder="Enter your group">
            </div>
            <button type="submit" name="register" class="btnlgn">Register</button>
            <p>Already have an account? <a href="#" id="show-login">Sign In</a></p>
        </form>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/login.js"></script>
</body>
</html>
