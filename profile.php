<?php
require 'config.php';
require_login();

// Show user data
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT id,fullname,email,user_type,picture,created_at FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) { echo 'User not found'; exit; }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Profile</title>

<style>
/* GLOBAL */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #eef2f7;
}

/* CONTAINER */
.container {
    width: 90%;
    max-width: 900px;
    margin: 30px auto;
}

/* NAVBAR */
.nav {
    background: #1e3a8a;
    color: white;
    padding: 14px 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
}
.nav a {
    color: #e0e7ff;
    text-decoration: none;
    margin-left: 15px;
}
.nav a:hover { color: #fff; }

/* PROFILE CARD */
.profile-card {
    margin-top: 20px;
    display: flex;
    gap: 20px;
    padding: 20px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    align-items: center;
}

/* AVATAR */
.avatar {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #1e3a8a;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}

/* NAME & INFO */
.user-info strong {
    font-size: 22px;
}
.small {
    color: #555;
    font-size: 14px;
}

/* DELETE ACCOUNT LINK */
.delete-btn {
    margin-top: 12px;
    display: inline-block;
    padding: 6px 12px;
    background: #dc2626;
    color: white !important;
    border-radius: 6px;
    text-decoration: none;
}
.delete-btn:hover { background: #b91c1c; }

/* SECTION TITLE */
.section-title {
    margin-top: 25px;
    font-size: 22px;
    color: #1e3a8a;
    font-weight: bold;
}

/* DTR TABLE */
table {
    width: 100%;
    margin-top: 10px;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 12px rgba(0,0,0,0.1);
}
thead {
    background: #1e40af;
    color: white;
}
th, td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #e5e7eb;
}
tbody tr:hover {
    background: #f3f4f6;
}
</style>
</head>

<body>

<div class="container">

  <!-- NAVBAR -->
  <div class="nav">
    <div>Welcome, <strong><?php echo htmlspecialchars($user['fullname']); ?></strong> (<?php echo htmlspecialchars($user['user_type']); ?>)</div>
    <div style="margin-left:auto">
      <?php if(is_admin()): ?>
        <a href="admin_dashboard.php"> Dashboard</a> |
      <?php endif; ?>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <!-- PROFILE CARD -->
  <div class="profile-card">
    <div>
      <?php if($user['picture'] && file_exists($user['picture'])): ?>
        <img src="<?php echo htmlspecialchars($user['picture']); ?>" class="avatar" alt="avatar">
      <?php else: ?>
        <div style="
            width:110px;height:110px;border-radius:50%;
            background:#dbeafe;color:#1e3a8a;
            display:flex;align-items:center;
            justify-content:center;font-size:40px;
            border:3px solid #1e3a8a;
            box-shadow:0 3px 10px rgba(0,0,0,0.2);
        ">
            <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="user-info">
      <strong><?php echo htmlspecialchars($user['fullname']); ?></strong>
      <div class="small"><?php echo htmlspecialchars($user['email']); ?></div>
      <div class="small">Member since: <?php echo htmlspecialchars($user['created_at']); ?></div>

      <a href="delete_account.php" class="delete-btn"
         onclick="return confirm('Delete your account? This cannot be undone.');">
         Delete my account
      </a>
    </div>
  </div>

  <!-- DTR SECTION -->
  <div class="section-title">DTR SYSTEM</div>
  <p class="small">(Scheduler)</p>

  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Breaks</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
       <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </tbody>
  </table>

</div>

</body>
</html>
