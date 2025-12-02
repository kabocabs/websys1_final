<?php
require 'config.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        // login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['fullname'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['user_picture'] = $user['picture'];
        header('Location: profile.php');
        exit;
    } else {
        $err = 'Invalid credentials.';
    }
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
  <h2>Login</h2>
  <?php if(isset($_GET['registered'])) echo '<div class="small" style="background:#eef7ef;padding:8px;border-radius:4px;margin-bottom:12px">Registration successful. Please login.</div>';?>
  <?php if($err) echo '<div style="background:#ffefef;padding:8px;border-radius:4px;margin-bottom:12px" class="small">'.htmlspecialchars($err).'</div>'; ?>

  <form method="post">
    <div class="form-row"><label>Email</label><input name="email" type="email" required></div>
    <div class="form-row"><label>Password</label><input name="password" type="password" required></div>
    <button type="submit">Login</button>
  </form>
  <p class="small">No account? <a href="register.php">Register</a></p>
</div>
</body>
</html>
