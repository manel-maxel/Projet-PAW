<?php
if (!isset($_SESSION)) { session_start(); }

if (!isset($_SESSION['name'])) {
    header("Location: LOGIN/login.php");
    exit();
}
?>

<link href="header/header.css" rel="stylesheet">

<header>
    <div class="logo-title">
        <img src='../images/logo.png' alt='Logo'>
        <h2>Attendance System</h2>
    </div>

    <div class="user-info">
        <span>👤 <?= $_SESSION['name']; ?></span>
        <a href="../logout.php">Logout</a>
    </div>
</header>
