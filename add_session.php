<?php
session_start();

if (!isset($_SESSION['professor_id'])) {
    header("Location: LOGIN/login.php");
    exit();
}

require_once "LOGIN/config.php";

$professor_id = $_SESSION['professor_id'];
$message = "";

$sql = "SELECT id, title FROM courses WHERE professor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_id = $_POST['course_id'] ?? '';
    $session_type = $_POST['session_type'] ?? '';
    $session_time = $_POST['session_time'] ?? '';

    if ($course_id && $session_type && $session_time) {

        $sql = "INSERT INTO sessions (course_id, title, time) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $course_id, $session_type, $session_time);

        if ($stmt->execute()) {
            $message = "<p class='success'>Session ajoutée avec succès !</p>";
        } else {
            $message = "<p class='error'>Erreur lors de l'ajout.</p>";
        }

    } else {
        $message = "<p class='error'>Veuillez remplir tous les champs.</p>";
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Session</title>
    <link href="/header/header.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 50%;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fdfdfd;
        }

        h2 {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        select, input[type="datetime-local"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        button {
            margin-top: 20px;
            padding: 10px;
            width: 100%;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .success {
            color: green;
            margin-top: 10px;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

    </style>

</head>
<body>

<?php include 'header/header.php'; ?>

<div class="container">
    <h2>Ajouter une séance (TD / TP)</h2>

    <?= $message ?>

    <form method="POST">

        <label>Cours :</label>
        <select name="course_id" required>
            <option value="">-- Choisir un cours --</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>">
                    <?= htmlspecialchars($course['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Type de séance :</label>
        <select name="session_type" required>
            <option value="">-- Choisir --</option>
            <option value="TD">TD</option>
            <option value="TP">TP</option>
        </select>

        <label>Date & Heure :</label>
        <input type="datetime-local" name="session_time" required>

        <button type="submit">Ajouter la séance</button>
    </form>
</div>

</body>
</html>
