<?php
require_once '../config.php';
unset($_SESSION['admin_id']);
header('Location: /admin/login.php');
exit;
