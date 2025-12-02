<?php
require 'config.php';
require_login();
if (!is_admin()) { 
    header('HTTP/1.1 403 Forbidden'); 
    echo 'Forbidden'; 
    exit; 
}

// SEARCH + SORT
$q = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'id';
$order = (isset($_GET['order']) && strtolower($_GET['order']) === 'desc') ? 'DESC' : 'ASC';

$allowedSort = ['id','fullname','email','user_type','created_at'];
if (!in_array($sort, $allowedSort)) $sort = 'id';

$sql = 'SELECT id, fullname, email, user_type, created_at FROM users WHERE 1=1';
$params = [];

if ($q !== '') {
    $sql .= ' AND (fullname LIKE ? OR email LIKE ? OR user_type LIKE ?)';
    $like = "%{$q}%";
    $params = [$like, $like, $like];
}

$sql .= " ORDER BY $sort $order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

function toggle_order($col) {
    $o = $_GET['order'] ?? 'asc';
    return (($_GET['sort'] ?? '') === $col && strtolower($o) === 'asc') ? 'desc' : 'asc';
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Dashboard</title>

<!-- ICONS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
}
.container {
    width: 90%;
    margin: 30px auto;
    background: #fff;
    padding: 20px;
    box-shadow: 0 3px 7px rgba(0,0,0,0.15);
    border-radius: 8px;
}
.nav { display: flex; margin-bottom: 20px; }
.nav a { margin-left: 10px; text-decoration: none; color: #007bff; }

table { width: 100%; border-collapse: collapse; }
thead { background: #007bff; color: white; }
td, th { border: 1px solid #ccc; padding: 10px; }
tr:hover { background: #f1f5ff; }

.actions { display: flex; gap: 8px; justify-content: center; }
.actions a { width: 32px; height: 32px; display: inline-flex; align-items:center; justify-content:center; border-radius:6px; color:#fff; }
.edit-btn { background: #007bff; }
.delete-btn { background: #dc3545; }
</style>
</head>

<body>
<div class="container">

  <div class="nav">
    <strong style="font-size:20px;">Admin Dashboard</strong>
    <div style="margin-left:auto;">
      <a href="profile.php">Back to Profile</a> |
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <div style="display:flex;justify-content:space-between;align-items:center">
    <form method="get" style="display:flex;gap:6px;">
      <input name="q" placeholder="Search name/email/type" value="<?php echo htmlspecialchars($q); ?>">
      <button type="submit">Search</button>
    </form>

    <a href="add_user.php" style="padding:8px 12px;background:#28a745;color:#fff;border-radius:6px;text-decoration:none;">
      + Add User
    </a>
  </div>

  <table>
    <thead>
      <tr>
        <th><a style="color:white;" href="?<?php echo http_build_query(array_merge($_GET,['sort'=>'id','order'=>toggle_order('id')])); ?>">ID</a></th>
        <th><a style="color:white;" href="?<?php echo http_build_query(array_merge($_GET,['sort'=>'fullname','order'=>toggle_order('fullname')])); ?>">Fullname</a></th>
        <th><a style="color:white;" href="?<?php echo http_build_query(array_merge($_GET,['sort'=>'email','order'=>toggle_order('email')])); ?>">Email</a></th>
        <th><a style="color:white;" href="?<?php echo http_build_query(array_merge($_GET,['sort'=>'user_type','order'=>toggle_order('user_type')])); ?>">Type</a></th>
        <th><a style="color:white;" href="?<?php echo http_build_query(array_merge($_GET,['sort'=>'created_at','order'=>toggle_order('created_at')])); ?>">Created</a></th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach($users as $u): ?>
        <tr>
          <td><?= $u['id']; ?></td>
          <td><?= htmlspecialchars($u['fullname']); ?></td>
          <td><?= htmlspecialchars($u['email']); ?></td>
          <td><?= htmlspecialchars($u['user_type']); ?></td>
          <td><?= htmlspecialchars($u['created_at']); ?></td>

          <td class="actions">
            <a class="edit-btn" href="edit_user.php?id=<?= $u['id']; ?>">
              <i class="fas fa-edit"></i>
            </a>
            <a class="delete-btn" href="delete_user.php?id=<?= $u['id']; ?>" onclick="return confirm('Delete this user?');">
              <i class="fas fa-trash"></i>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</div>
</body>
</html>
