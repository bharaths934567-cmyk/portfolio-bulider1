<?php
require_once 'config.php';
require_once 'includes/renderer.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, t.layout_type, t.accent_color, t.name AS tpl_name
                       FROM portfolios p JOIN templates t ON t.id = p.template_id
                       WHERE p.id = ? AND p.user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$p) { die('Portfolio not found.'); }

// The rendered HTML already embeds its own CSS — strip closing tags to inject a toolbar.
$html = str_replace('</body></html>', '', render_portfolio($p, $p));
?>
<?= $html ?>
<style>
  .toolbar { position:fixed; top:0; left:0; right:0; background:#1d2333; padding:12px;
             text-align:center; z-index:99; box-shadow:0 2px 8px rgba(0,0,0,.3); }
  .toolbar a, .toolbar button { display:inline-block; margin:0 8px; padding:10px 22px;
             border-radius:8px; text-decoration:none; font-weight:bold; border:none; cursor:pointer; }
  .btn-html { background:#4f6df5; color:#fff; }
  .btn-pdf  { background:#e14f7a; color:#fff; font-size:.95rem; }
  body { padding-top:64px; }
  @media print { .toolbar { display:none !important; } body { padding-top:0; } }
</style>
<div class="toolbar">
  <b style="color:#fff; margin-right:14px;">🎉 Your portfolio is ready! Choose a file type:</b>
  <a class="btn-html" href="export.php?id=<?= $id ?>&type=html">⬇ Download HTML</a>
  <a class="btn-pdf" href="download-pdf.php?id=<?= $id ?>">Download PDF</a>
  <a class="btn-html" style="background:#555" href="my_portfolios.php">← All Portfolios</a>
</div>
</body></html>
