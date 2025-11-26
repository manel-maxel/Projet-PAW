<?php
session_start();
session_destroy();
header("Location: LOGIN/login.php");
exit();
?>
