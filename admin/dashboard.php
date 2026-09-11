<?php
require_once '../config.php';
require_admin();

// Delete template
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM templates WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: dashboard.php'); exit;
}

$msg = '';
$error = '';
// Add / Edit template
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();
  try {
  $name = trim($_POST['name'] ?? '');
  $category = trim($_POST['category'] ?? 'Modern');
  $desc = trim($_POST['description'] ?? '');
  $layout = in_array($_POST['layout_type'] ?? '', ['modern','classic','creative'], true) ? $_POST['layout_type'] : 'modern';
  $color = preg_match('/^#[0-9a-fA-F]{6}$/', $_POST['accent_color'] ?? '') ? $_POST['accent_color'] : '#4f6df5';
  $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
  if ($name === '') throw new RuntimeException('Template name is required.');
  $templateFile = upload_file($_FILES['template_file'] ?? [], 'php', ['application/x-php' => 'php', 'text/x-php' => 'php', 'text/plain' => 'php'], 1048576);
  $previewImage = upload_file($_FILES['preview_image'] ?? [], 'previews', ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp']);
  if (!empty($_POST['id'])) {
    $values = [$name, $category, $desc, $layout, $color, $status];
    $sql = 'UPDATE templates SET name=?, category=?, description=?, layout_type=?, accent_color=?, status=?';
    if ($templateFile) { $sql .= ', template_file=?'; $values[] = $templateFile; }
    if ($previewImage) { $sql .= ', preview_image=?'; $values[] = $previewImage; }
    $sql .= ' WHERE id=?'; $values[] = (int)$_POST['id'];
    $pdo->prepare($sql)->execute($values);
    $msg = 'Template updated.';
  } else {
    $pdo->prepare('INSERT INTO templates (name, category, description, template_file, preview_image, layout_type, accent_color, status) VALUES (?,?,?,?,?,?,?,?)')->execute([$name, $category, $desc, $templateFile ?: 'modern.php', $previewImage, $layout, $color, $status]);
    $msg = 'Template added.';
  }
  } catch (Throwable $exception) { $error = $exception->getMessage(); }
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM templates WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
$templates = $pdo->query("SELECT * FROM templates ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$users     = $pdo->query("SELECT COUNT(*) c FROM users")->fetch(PDO::FETCH_ASSOC)['c'];
$portfolios= $pdo->query("SELECT COUNT(*) c FROM portfolios")->fetch(PDO::FETCH_ASSOC)['c'];
$active    = $pdo->query("SELECT COUNT(*) c FROM templates WHERE status = 'active'")->fetch(PDO::FETCH_ASSOC)['c'];
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Admin Dashboard</title>
<link rel="stylesheet" href="../assets/style.css"></head><body>
<nav class="topnav"><span class="brand">🛠 Admin Panel</span>
<a href="logout.php">Logout</a></nav>
<div class="wrap">
  <p><b><?= $users ?></b> users · <b><?= $portfolios ?></b> portfolios · <b><?= count($templates) ?></b> templates · <b><?= $active ?></b> active</p>
  <?php if ($msg): ?><p class="success"><?= e($msg) ?></p><?php endif; ?>
  <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>

  <fieldset style="margin:20px 0"><legend><?= $edit ? 'Edit Template' : 'Add New Template' ?></legend>
    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
      <input name="name" placeholder="Template name *" value="<?= e($edit['name'] ?? '') ?>" required>
      <input name="description" placeholder="Short description" value="<?= e($edit['description'] ?? '') ?>">
      <input name="category" placeholder="Category" value="<?= e($edit['category'] ?? 'Modern') ?>" required>
      <label>Template PHP file <input type="file" name="template_file" accept=".php,text/php"></label>
      <label>Preview image <input type="file" name="preview_image" accept="image/jpeg,image/png,image/webp"></label>
      <select name="layout_type">
        <?php foreach (['modern','classic','creative'] as $l): ?>
        <option value="<?= $l ?>" <?= ($edit['layout_type'] ?? 'modern') === $l ? 'selected' : '' ?>><?= ucfirst($l) ?> layout</option>
        <?php endforeach; ?>
      </select>
      <label>Accent color: <input type="color" name="accent_color" value="<?= e($edit['accent_color'] ?? '#4f6df5') ?>"></label>
      <select name="status"><option value="active" <?= ($edit['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= ($edit['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select>
      <button class="btn"><?= $edit ? 'Update' : 'Add Template' ?></button>
      <?php if ($edit): ?><a href="dashboard.php">Cancel</a><?php endif; ?>
    </form>
  </fieldset>

  <h2>Templates (<?= count($templates) ?>)</h2>
  <table class="tbl">
    <tr><th>ID</th><th>Preview</th><th>Name</th><th>Category</th><th>Status</th><th>Actions</th></tr>
    <?php foreach ($templates as $t): ?>
    <tr>
      <td><?= $t['id'] ?></td><td><?php if (!empty($t['preview_image'])): ?><img class="template-thumb" src="../templates/<?= e($t['preview_image']) ?>" alt=""><?php else: ?><span class="template-thumb empty">No image</span><?php endif; ?></td><td><?= e($t['name']) ?></td>
      <td><?= e($t['category']) ?></td><td><?= e($t['status']) ?></td>
      <td>
        <a class="btn small" href="?edit=<?= $t['id'] ?>">Edit</a>
        <form method="post" action="toggle-template.php" style="display:inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$t['id'] ?>"><button class="btn small ghost" type="submit"><?= ($t['status'] ?? 'active') === 'active' ? 'Deactivate' : 'Activate' ?></button></form>
        <a class="btn small danger" href="?delete=<?= $t['id'] ?>" onclick="return confirm('Delete this template?')">Delete</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div></body></html>
