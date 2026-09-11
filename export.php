<?php
require_once 'config.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, t.layout_type, t.accent_color, t.name AS tpl_name
                       FROM portfolios p JOIN templates t ON t.id = p.template_id
                       WHERE p.id = ? AND p.user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$p || ($_GET['type'] ?? '') !== 'html') { die('Invalid request.'); }

require_once 'includes/renderer.php';
$html = render_portfolio($p, $p, true);
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="portfolio_' . preg_replace('/[^a-z0-9]+/i', '_', $p['full_name']) . '.html"');
echo $html;
exit;
