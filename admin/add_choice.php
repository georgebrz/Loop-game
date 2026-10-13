<?php
include("../db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $scene_id = $_POST['scene_id'];
    $choice_text = trim($_POST['choice_text']);
    $next_scene_id = $_POST['next_scene_id'];

    if (!is_numeric($scene_id) || !is_numeric($next_scene_id) || empty($choice_text)) {
        die("Invalid input.");
    }

    $stmt = $conn->prepare("INSERT INTO choices (scene_id, choice_text, next_scene_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $scene_id, $choice_text, $next_scene_id);
    $stmt->execute();
    $stmt->close();

    header("Location: choices.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Choice</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container">
    <h1>Add Choice</h1>

    <div class="form-box">
        <form method="POST">
            <label for="scene_id">Scene ID</label>
            <input type="number" name="scene_id" id="scene_id" required>

            <label for="choice_text">Choice Text</label>
            <input type="text" name="choice_text" id="choice_text" required>

            <label for="next_scene_id">Next Scene ID</label>
            <input type="number" name="next_scene_id" id="next_scene_id" required>

            <div class="form-actions">
                <button class="main-button" type="submit">Add Choice</button>
                <a class="secondary-button" href="choices.php">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
