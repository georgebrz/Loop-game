<?php
include("../db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid scene ID.");
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM scenes WHERE scene_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$scene = $result->fetch_assoc();
$stmt->close();

if (!$scene) {
    die("Scene not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $is_ending = isset($_POST['is_ending']) ? 1 : 0;

    if (empty($title) || empty($content)) {
        die("Invalid input.");
    }

    $stmt = $conn->prepare("UPDATE scenes SET title = ?, content = ?, is_ending = ? WHERE scene_id = ?");
    $stmt->bind_param("ssii", $title, $content, $is_ending, $id);
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
    <title>Edit Scene</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container">
    <h1>Edit Scene</h1>

    <div class="form-box">
        <form method="POST">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($scene['title']); ?>" required>

            <label for="content">Content</label>
            <textarea name="content" id="content" required><?php echo htmlspecialchars($scene['content']); ?></textarea>

            <label>
                <input type="checkbox" name="is_ending" <?php if ($scene['is_ending']) echo "checked"; ?>>
                Is this an ending scene?
            </label>

            <div class="form-actions">
                <button class="main-button" type="submit">Update Scene</button>
                <a class="secondary-button" href="scenes.php">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
