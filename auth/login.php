<?php
require_once '../config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([trim($_POST['email'] ?? '')]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
      session_regenerate_id(true);
        $_SESSION['user_id']   = $u['id'];
        $_SESSION['user_name'] = $u['name'];
        header('Location: ../index.php');
        exit;
    }
    $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Login</title>
<link rel="stylesheet" href="../assets/style.css"></head><body>
<div class="auth-card">
  <h2>Login</h2>
  <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input name="email" type="email" placeholder="Email" required>
    <input name="password" type="password" placeholder="Password" required>
    <button class="btn">Login</button>
  </form>
  <p>New here? <a href="register.php">Create an account</a></p>
</div></body></html>
