<?php
require_once '../config.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: dashboard.php'); exit; }
verify_csrf();
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id) {
    $stmt = $pdo->prepare("UPDATE templates SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: dashboard.php');
exit;
