<?php
require 'config.php';
require_login();

// Only admin can access
if (!is_admin()) { 
    header('HTTP/1.1 403 Forbidden'); 
    echo 'Forbidden'; 
    exit; 
}

$id = intval($_GET['id'] ?? 0);

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $user_type = trim($_POST["user_type"]); // admin or faculty
    $password = $_POST["password"] ?? "";

    if ($password !== "") {
        // Update with password change
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET fullname=?, email=?, user_type=?, password=? WHERE id=?";
        $params = [$fullname, $email, $user_type, $hashed_password, $id];
    } else {
        // Update without changing password
        $sql = "UPDATE users SET fullname=?, email=?, user_type=? WHERE id=?";
        $params = [$fullname, $email, $user_type, $id];
    }

    $update = $pdo->prepare($sql);
    $update->execute($params);

    header("Location: admin_dashboard.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit User</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container admin-container">
    <h2>Edit User</h2>

    <form method="POST">

        <div class="form-row">
            <label>Full Name:</label>
            <input name="fullname" value="<?= htmlspecialchars($user['fullname']); ?>" required>
        </div>

        <div class="form-row">
            <label>Email:</label>
            <input name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="form-row">
            <label>User Type:</label>
            <select name="user_type">
                <option value="admin" <?= $user['user_type'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="faculty" <?= $user['user_type'] === 'faculty' ? 'selected' : '' ?>>Faculty</option>
            </select>
        </div>

        <div class="form-row">
            <label>New Password (optional):</label>
            <input type="password" name="password" placeholder="Leave blank to keep current password">
        </div>

        <button type="submit">Save Changes</button>

        <div style="margin-top:10px;">
            <a href="admin_dashboard.php">← Back to Dashboard</a>
        </div>

    </form>
</div>

</body>
</html>
