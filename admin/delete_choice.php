<?php
include("../db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid choice ID.");
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("DELETE FROM choices WHERE choice_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: choices.php");
exit();
