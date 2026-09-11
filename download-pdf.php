<?php
require_once 'config.php';
require_login();
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) { http_response_code(404); exit('Portfolio not found.'); }
$stmt = $pdo->prepare("SELECT p.*, t.template_file, t.template_html, t.template_css, t.fields_json, t.layout_type, t.accent_color, t.name AS tpl_name FROM portfolios p JOIN templates t ON t.id = p.template_id WHERE p.id = ? AND p.user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$portfolio = $stmt->fetch();
if (!$portfolio) { http_response_code(404); exit('Portfolio not found.'); }
require_once 'includes/renderer.php';
$html = render_portfolio($portfolio, $portfolio, true);
$autoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoload)) { http_response_code(503); exit('Install Dompdf with Composer to enable direct PDF downloads.'); }
require_once $autoload;
$dompdf = new Dompdf\Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream('portfolio_' . preg_replace('/[^a-z0-9]+/i', '_', $portfolio['full_name']) . '.pdf', ['Attachment' => true]);
