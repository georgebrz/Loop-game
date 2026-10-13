<?php
include("../db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid choice ID.");
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM choices WHERE choice_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$choice = $result->fetch_assoc();
$stmt->close();

if (!$choice) {
    die("Choice not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $scene_id = $_POST['scene_id'];
    $choice_text = trim($_POST['choice_text']);
    $next_scene_id = $_POST['next_scene_id'];

    if (!is_numeric($scene_id) || !is_numeric($next_scene_id) || empty($choice_text)) {
        die("Invalid input.");
    }

    $stmt = $conn->prepare("UPDATE choices SET scene_id = ?, choice_text = ?, next_scene_id = ? WHERE choice_id = ?");
    $stmt->bind_param("isii", $scene_id, $choice_text, $next_scene_id, $id);
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
    <title>Edit Choice</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container">
    <h1>Edit Choice</h1>

    <div class="form-box">
        <form method="POST">
            <label for="scene_id">Scene ID</label>
            <input type="number" name="scene_id" id="scene_id" value="<?php echo htmlspecialchars($choice['scene_id']); ?>" required>

            <label for="choice_text">Choice Text</label>
            <input type="text" name="choice_text" id="choice_text" value="<?php echo htmlspecialchars($choice['choice_text']); ?>" required>

            <label for="next_scene_id">Next Scene ID</label>
            <input type="number" name="next_scene_id" id="next_scene_id" value="<?php echo htmlspecialchars($choice['next_scene_id']); ?>" required>

            <div class="form-actions">
                <button class="main-button" type="submit">Update Choice</button>
                <a class="secondary-button" href="choices.php">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
