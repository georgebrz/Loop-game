<?php
include("../db.php");

$sql = "SELECT * FROM scenes ORDER BY scene_id ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Scenes</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container">
    <h1>Scenes</h1>

    <div class="form-actions">
        <a class="main-button" href="add_scene.php">Add Scene</a>
        <a class="secondary-button" href="index.php">Back</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Ending</th>
            <th>Actions</th>
        </tr>

        <?php if ($result->num_rows == 0) : ?>
        <tr>
            <td colspan="4">No scenes found.</td>
        </tr>
        <?php else : ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo htmlspecialchars($row['scene_id']); ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo $row['is_ending'] ? 'Yes' : 'No'; ?></td>
                <td>
                    <a href="edit_scene.php?id=<?php echo $row['scene_id']; ?>">Edit</a> |
                    <a class="delete-link" href="delete_scene.php?id=<?php echo $row['scene_id']; ?>" onclick="return confirm('Are you sure you want to delete this scene?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>
</div>

</body>
</html>


 
