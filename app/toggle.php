<?php
require_once __DIR__ . '/config.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('UPDATE tasks SET is_done = NOT is_done WHERE id = ?');
$stmt->bind_param('i', $id); $stmt->execute();
header('Location: index.php'); exit;
