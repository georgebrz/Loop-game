<?php
include("../db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $is_ending = isset($_POST['is_ending']) ? 1 : 0;

    if (empty($title) || empty($content)) {
        die("Invalid input.");
    }

    $stmt = $conn->prepare("INSERT INTO scenes (title, content, is_ending) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $content, $is_ending);
    $stmt->execute();
    $stmt->close();

    header("Location: scenes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Scene</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container">
    <h1>Add Scene</h1>

    <div class="form-box">
        <form method="POST">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" required>

            <label for="content">Content</label>
            <textarea name="content" id="content" required></textarea>

            <label>
                <input type="checkbox" name="is_ending"> Is this an ending scene?
            </label>

            <div class="form-actions">
                <button class="main-button" type="submit">Add Scene</button>
                <a class="secondary-button" href="scenes.php">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
