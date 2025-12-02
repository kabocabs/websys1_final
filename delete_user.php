<?php
require 'config.php';
require_login();
if (!is_admin()) { header('HTTP/1.1 403 Forbidden'); echo 'Forbidden'; exit; }

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_dashboard.php'); exit; }

// get picture path if any
$stmt = $pdo->prepare('SELECT picture FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();

// delete
$stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
$stmt->execute([$id]);

if ($user && $user['picture'] && file_exists($user['picture'])) @unlink($user['picture']);

header('Location: admin_dashboard.php');
exit;
?>
