<?php
include("../db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid scene ID.");
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("DELETE FROM scenes WHERE scene_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: scenes.php");
exit();
