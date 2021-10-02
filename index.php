<?php

session_start();
session_regenerate_id();
require_once 'utils/helpers.php';
$page_title = 'Home';

?>

<?php include 'partials/header.php' ?>
<?php include 'partials/navbar.php' ?>
<div class="hero text-center">
  <hgroup>
      <h1 class="mt-3">Best Blog<span class="text-muted">g</span> in town</h1>        
      <h3 class="mt-2">Do not waste anymore time and start blogging today with all your friends</h3>
  </hgroup>
  <a class="btn btn-primary btn-lg mt-3" href="/register.php">Start blogging</a>
</div>
<?php include 'partials/footer.php' ?>
<?php include 'partials/end.php' ?>