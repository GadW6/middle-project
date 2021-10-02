<?php

session_start();
session_regenerate_id();
require_once 'utils/helpers.php';
$page_title = '403';

?>

<?php include 'partials/header.php' ?>
  <style>
    body::after {background-color: rgba(0, 0, 0, 0.75);}
    body {color: white;}
    body .main-container {height: 100vh !important;}
    body .main-container .hero {margin-top: 30vh;}
    nav {display: none !important;}
    footer {display: none;}
  </style>
<?php include 'partials/navbar.php' ?>
<div class="hero text-center">
  <h1 class="display-1 text-danger">Access Denied <span class="text-muted">|</span> 403</h1>
  <h3 class="mt-4">User beware ! Any wrongdoing on our site will be monitored and acted upon !</h3>
</div>
<?php include 'partials/footer.php' ?>
  <script>
    setTimeout(() => {
      window.location.replace("/");
    }, 3000)
  </script>
<?php include 'partials/end.php' ?>