<?php require_once 'config.php'; ?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Portfolio Builder</title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<nav class="topnav">
  <span class="brand">🚀 Portfolio Builder</span>
  <span>
    <?php if (!empty($_SESSION['user_id'])): ?>
      Hi, <?= e($_SESSION['user_name']) ?> |
      <a href="my_portfolios.php">My Portfolios</a> |
      <a href="auth/logout.php">Logout</a>
    <?php else: ?>
      <a href="auth/login.php">Login</a> | <a href="auth/register.php">Register</a>
    <?php endif; ?>
  </span>
</nav>
<div class="hero">
  <h1>Build a stunning portfolio in minutes</h1>
  <p>Pick a template → add your details → export as HTML or PDF.</p>
  <?php if (!empty($_SESSION['user_id'])): ?>
    <a class="btn big" href="choose_template.php">▶ Start</a>
  <?php else: ?>
    <a class="btn big" href="auth/login.php">▶ Start</a>
  <?php endif; ?>
</div></body></html>
