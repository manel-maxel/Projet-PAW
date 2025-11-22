<?php
require_once "config.php";

function connectDB() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;

    try {
        $conn = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8", $DB_USER, $DB_PASS);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn; 

    } catch (PDOException $e) {

        $logMessage = "[" . date("Y-m-d H:i:s") . "] CONNECTION FAILED: " . $e->getMessage() . "\n";
        file_put_contents("db_errors.log", $logMessage, FILE_APPEND);

        return false; 
    }
}
?>
