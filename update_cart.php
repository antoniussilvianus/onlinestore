<?php
if (session_status()===PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if(isset($_GET['remove'])){
  $k = $_GET['remove'];
  unset($_SESSION['cart'][$k]);
  header('Location: cart.php');
  exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qty'])){
  foreach($_POST['qty'] as $k => $q){
    $q = max(1, (int)$q);
    if(isset($_SESSION['cart'][$k])){
      $_SESSION['cart'][$k]['qty'] = $q;
    }
  }
}
header('Location: cart.php');
?>
