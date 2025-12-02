<?php
require 'config.php';
require_login();
if (!is_admin()) { 
    header('HTTP/1.1 403 Forbidden'); 
    echo 'Forbidden'; 
    exit; 
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $user_type = ($_POST['user_type'] === 'admin') ? 'admin' : 'faculty';

    // Picture Upload
    $picture_path = null;
    if (!empty($_FILES['picture']['name'])) {

        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = 'Invalid image type (allowed: JPG, PNG, GIF).';
        } else {
            // Create folder if not exists
            if (!is_dir('uploads')) mkdir('uploads');

            $filename = 'uploads/' . time() . '_' . basename($_FILES['picture']['name']);
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $filename)) {
                $picture_path = $filename;
            } else {
                $errors[] = 'Failed to upload picture.';
            }
        }
    }

    if (!$fullname) $errors[] = 'Fullname required.';
    if (!$email) $errors[] = 'Valid email required.';
    if (strlen($password) < 6) $errors[] = 'Password must be 6+ characters.';

    if (empty($errors)) {
        
        // Check duplicate email
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = 'Email already exists.';
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('INSERT INTO users (fullname,email,password,user_type,picture) 
                                   VALUES (?,?,?,?,?)');

            $stmt->execute([$fullname, $email, $hash, $user_type, $picture_path]);

            header('Location: admin_dashboard.php');
            exit;
        }
    }
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Add User</title>

<!-- Simple built-in styling for clean UI -->
<style>
body {
    font-family: Arial, sans-serif;
    background: #eef2f7;
    margin: 0;
}
.container {
    width: 420px;
    margin: 40px auto;
    background: white;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.1);
}
h2 {
    color: #1e3a8a;
    margin-bottom: 15px;
}
.form-row {
    margin-bottom: 12px;
}
label {
    display: block;
    margin-bottom: 4px;
    font-weight: bold;
    color: #333;
}
input, select {
    width: 100%;
    padding: 9px;
    border: 1px solid #ccc;
    border-radius: 6px;
}
button {
    width: 100%;
    background: #1e3a8a;
    color: white;
    padding: 10px;
    border: 0;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
}
button:hover {
    background: #162d6b;
}
.error {
    background: #ffe0e0;
    padding: 8px;
    margin-bottom: 8px;
    color: #b91c1c;
    border-left: 4px solid #dc2626;
}
a { color: #1e3a8a; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
</head>

<body>
<div class="container">

  <h2>Add User</h2>

  <?php 
  if ($errors) {
      foreach ($errors as $e) {
          echo '<div class="error">'.htmlspecialchars($e).'</div>';
      }
  }
  ?>

  <form method="post" enctype="multipart/form-data">

    <div class="form-row">
      <label>Fullname</label>
      <input name="fullname" required>
    </div>

    <div class="form-row">
      <label>Email</label>
      <input name="email" type="email" required>
    </div>

    <div class="form-row">
      <label>Password</label>
      <input name="password" type="password" required>
    </div>

    <div class="form-row">
      <label>User Type</label>
      <select name="user_type">
        <option value="faculty">Faculty</option>
        <option value="admin">Admin</option>
      </select>
    </div>

    <div class="form-row">
      <label>Profile Picture</label>
      <input type="file" name="picture" accept="image/*">
    </div>

    <button type="submit">Add User</button>
  </form>

  <p style="margin-top:12px;">
    <a href="admin_dashboard.php">◀ Back to Dashboard</a>
  </p>

</div>
</body>
</html>
