<?php

session_start();
session_regenerate_id();
require_once 'utils/helpers.php';

if (!auth_user()) {
  header('location: login.php');
  die;
}

$userId = $_SESSION['user_id'];

if (isset($_GET['q']) && $userId) {
  $comtId = $_GET['q'];
  $mysql = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE, DB_PORT);
  $userId = mysqli_real_escape_string($mysql, $userId);
  $query = "DELETE FROM comments WHERE id='$comtId' AND user_id='$userId';";
  mysqli_query($mysql, $query);
  if (mysqli_affected_rows($mysql) > 0) {
    header('location: post.php?q=' . $_GET['p']);
  } else {
    header('location: 403.php');
  }
}

?>