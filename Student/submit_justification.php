<?php
session_start();
require_once "../LOGIN/config.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = intval($_POST['session_id']);
    $justification_text = trim($_POST['justification_text']);

    if ($session_id <= 0 || empty($justification_text)) {
        $_SESSION['justification_error'] = "Invalid session or empty justification.";
        header("Location: student_page.php"); 
        exit();
    }

    
    $stmt = $conn->prepare("SELECT id FROM justifications WHERE student_id = ? AND session_id = ?");
    $stmt->bind_param("ii", $student_id, $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      
        $stmt = $conn->prepare("UPDATE justifications SET justification_text = ?, status = 'pending' WHERE student_id = ? AND session_id = ?");
        $stmt->bind_param("sii", $justification_text, $student_id, $session_id);
        $stmt->execute();
    } else {
       
        $stmt = $conn->prepare("INSERT INTO justifications (student_id, session_id, justification_text, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("iis", $student_id, $session_id, $justification_text);
        $stmt->execute();
    }

    $_SESSION['justification_success'] = "Justification submitted successfully!";
    header("Location: student_page.php");
    exit();
} else {
   
    header("Location: student_page.php");
    exit();
}
?>
