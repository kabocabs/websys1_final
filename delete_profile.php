<?php
require 'config.php';
require_login();

$user_id = $_SESSION['user_id'];
// fetch picture path
$stmt = $pdo->prepare('SELECT picture FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// delete DB record
$stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
$stmt->execute([$user_id]);

// delete picture file if exists
if ($user && $user['picture'] && file_exists($user['picture'])) {
    @unlink($user['picture']);
}

session_unset();
session_destroy();
header('Location: register.php');
exit;
?>
