<?php
require_once '../config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($pass) >= 8 && $pass === ($_POST['confirm_password'] ?? '')) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
            $_SESSION['user_id']  = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            header('Location: ../index.php');
            exit;
        } catch (PDOException $ex) {
            $error = 'Email is already registered.';
        }
    } else {
        $error = 'Use a valid email and matching password of at least 8 characters.';
    }
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Register</title>
<link rel="stylesheet" href="../assets/style.css"></head><body>
<div class="auth-card">
  <h2>Create Account</h2>
  <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <form method="post">
        <?= csrf_field() ?><input name="name" placeholder="Full name" required>
    <input name="email" type="email" placeholder="Email" required>
    <input name="password" type="password" placeholder="Password (min 8 chars)" required>
    <input name="confirm_password" type="password" placeholder="Confirm password" required>
    <button class="btn">Register</button>
  </form>
  <p>Already have an account? <a href="login.php">Login</a></p>
</div></body></html>
