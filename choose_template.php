<?php
require_once 'config.php';
require_login();
$templates = $pdo->query("SELECT * FROM templates WHERE COALESCE(status, 'active') = 'active' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['template_id'])) {
  verify_csrf();
    $_SESSION['template_id'] = (int)$_POST['template_id'];
    header('Location: build.php');
    exit;
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Choose Template</title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<nav class="topnav"><span class="brand">🚀 Portfolio Builder</span>
<span><a href="index.php">← Back to Home</a> <a href="my_portfolios.php">My Portfolios</a></span></nav>
<div class="wrap">
  <h1>Step 1 · Choose a Template</h1>
  <div class="tpl-grid">
    <?php foreach ($templates as $t): ?>
      <form method="post" class="tpl-card">
        <?= csrf_field() ?>
        <input type="hidden" name="template_id" value="<?= $t['id'] ?>">
        <div class="tpl-preview" style="border-top:6px solid <?= e($t['accent_color']) ?>"><?php if (!empty($t['preview_data'])): ?><img src="<?= e($t['preview_data']) ?>" alt="<?= e($t['name']) ?> preview"><?php elseif (!empty($t['preview_image'])): ?><img src="templates/<?= e($t['preview_image']) ?>" alt="<?= e($t['name']) ?> preview"><?php endif; ?>
          <strong><?= e($t['name']) ?></strong>
          <small><?= e(ucfirst($t['layout_type'])) ?> layout</small>
        </div>
        <p><?= e($t['description']) ?></p>
        <button class="btn">Use this template</button>
      </form>
    <?php endforeach; ?>
  </div>
</div></body></html>
