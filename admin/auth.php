<?php
if (session_status()===PHP_SESSION_NONE) session_start();
if (empty($_SESSION['is_admin'])){
  $next = basename($_SERVER['REQUEST_URI']) ?: 'products.php';
  header('Location: login.php?next='.urlencode($next));
  exit;
}
?>
