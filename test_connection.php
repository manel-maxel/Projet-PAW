<?php
require_once "db_connect.php";

$conn = connectDB();

if ($conn) {
    echo "<h3 style='color:green;'>Connection successful ✔</h3>";
} else {
    echo "<h3 style='color:red;'>Connection failed ✘</h3>";
}
?>
