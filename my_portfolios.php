<?php
require_once 'config.php';
require_login();
$stmt = $pdo->prepare("SELECT p.id, p.full_name, p.created_at, t.name AS tpl_name
                       FROM portfolios p JOIN templates t ON t.id=p.template_id
                       WHERE p.user_id = ? ORDER BY p.id DESC");
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>My Portfolios</title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<nav class="topnav"><span class="brand">🚀 Portfolio Builder</span>
<a href="index.php">← Home</a></nav>
<div class="wrap"><h1>My Portfolios</h1>
<table class="tbl">
  <tr><th>Name</th><th>Template</th><th>Created</th><th></th></tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td><?= e($r['full_name']) ?></td><td><?= e($r['tpl_name']) ?></td>
    <td><?= e($r['created_at']) ?></td>
    <td><a class="btn small" href="generate.php?id=<?= $r['id'] ?>">Open / Export</a></td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="4">No portfolios yet. <a href="choose_template.php">Create one →</a></td></tr><?php endif; ?>
</table></div></body></html>
