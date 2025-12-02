<?php
require 'config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $user_type = ($_POST['user_type'] === 'admin') ? 'admin' : 'faculty';

    if (!$fullname) $errors[] = 'Fullname required.';
    if (!$email) $errors[] = 'Valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be 6+ chars.';
    if ($password !== $password2) $errors[] = 'Passwords do not match.';

    // handle picture upload
    $picturePath = null;
    if (!empty($_FILES['picture']['name'])) {
        $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array(strtolower($ext), $allowed)) {
            $errors[] = 'Invalid picture format.';
        } else {
            $newName = uniqid('p_', true) . '.' . $ext;
            $uploadDir = __DIR__ . '/uploads/';
            
            // CREATE uploads folder if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $dst = $uploadDir . $newName;
            if (!move_uploaded_file($_FILES['picture']['tmp_name'], $dst)) {
                $errors[] = 'Failed to upload picture.';
            } else {
                $picturePath = 'uploads/' . $newName;
            }
        }
    }

    if (empty($errors)) {
        // check duplicate email
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (fullname,email,password,user_type,picture) VALUES (?,?,?,?,?)');
            $stmt->execute([$fullname, $email, $hash, $user_type, $picturePath]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Register</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
  <h2>Register</h2>
  <?php if(!empty($errors)): ?>
    <div style="background:#ffefef;padding:8px;border-radius:4px;margin-bottom:12px">
      <?php foreach($errors as $e) echo '<div class="small">'.htmlspecialchars($e).'</div>'; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="form-row"><label>Fullname</label><input name="fullname" required></div>
    <div class="form-row"><label>Email</label><input name="email" type="email" required></div>
    <div class="form-row"><label>Password</label><input name="password" type="password" required></div>
    <div class="form-row"><label>Confirm Password</label><input name="password2" type="password" required></div>
    <div class="form-row"><label>User Type</label>
      <select name="user_type">
        <option value="faculty">Faculty</option>
        <option value="admin">Admin</option>
      </select>
    </div>
    <div class="form-row"><label>Picture (optional)</label><input name="picture" type="file" accept="image/*"></div>
    <button type="submit">Register</button>
  </form>
  <p class="small">Already registered? <a href="login.php">Login</a></p>
</div>
</body>
</html>
