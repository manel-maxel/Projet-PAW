<?php
session_start();
echo "<h1>Bienvenue Étudiant " . ($_SESSION['name'] ?? '') . "!</h1>";
echo "<p>Cette page est en construction.</p>";
echo "<a href='login.php'>Retour à la connexion</a>";
?>