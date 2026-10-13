<?php
include("../db.php");

$sql = "SELECT * FROM choices ORDER BY choice_id ASC";
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
    <title>Manage Choices</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container">
    <h1>Choices</h1>

    <div class="form-actions">
        <a class="main-button" href="add_choice.php">Add Choice</a>
        <a class="secondary-button" href="index.php">Back</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Scene ID</th>
            <th>Choice Text</th>
            <th>Next Scene</th>
            <th>Actions</th>
        </tr>

        <?php if ($result->num_rows == 0) : ?>
        <tr>
            <td colspan="5">No choices found.</td>
        </tr>
        <?php else : ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo htmlspecialchars($row['choice_id']); ?></td>
                <td><?php echo htmlspecialchars($row['scene_id']); ?></td>
                <td><?php echo htmlspecialchars($row['choice_text']); ?></td>
                <td><?php echo htmlspecialchars($row['next_scene_id']); ?></td>
                <td>
                    <a href="edit_choice.php?id=<?php echo $row['choice_id']; ?>">Edit</a> |
                    <a class="delete-link" href="delete_choice.php?id=<?php echo $row['choice_id']; ?>" onclick="return confirm('Are you sure you want to delete this choice?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
