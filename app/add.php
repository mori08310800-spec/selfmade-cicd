<?php
require_once __DIR__ . '/config.php';
$title = trim($_POST['title'] ?? '');
if ($title !== '') {
    $stmt = $conn->prepare('INSERT INTO tasks(title) VALUES (?)');
    $stmt->bind_param('s', $title);
    $stmt->execute();
}
header('Location: index.php'); exit;
