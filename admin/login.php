<?php
require_once '../config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([trim($_POST['username'] ?? '')]);
    $a = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($a && password_verify($_POST['password'] ?? '', $a['password'])) { session_regenerate_id(true); $_SESSION['admin_id'] = $a['id']; header('Location: dashboard.php'); exit; }
    $error = 'Invalid credentials.';
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Admin Login</title>
<link rel="stylesheet" href="../assets/style.css"></head><body>
<div class="auth-card">
  <h2>Admin Login</h2>
  <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input name="username" type="email" placeholder="Admin email" required>
    <input name="password" type="password" placeholder="Password" required>
    <button class="btn">Login</button>
  </form>
</div></body></html>
