<?php

session_start();
session_regenerate_id();
require_once 'utils/helpers.php';
$page_title = 'About';

?>

<?php include 'partials/header.php' ?>
  <link rel="stylesheet" href="public/css/about.css">
<?php include 'partials/navbar.php' ?>
<div class="hero text-center container">
  <h2>Learn about our team:</h2>
  <section class="bg-danger mx-auto mb-5" style="height: 2px;"></section>
  <div class="row mb-5">

    <div class="col-lg-4 mt-4">
        <div class="card mb-5 mb-lg-0">
          <div class="card-body mt-n5 pt-0">
            <img src="images/avatars/2020.04.11.22.02.45-debian.png" height="35%" width="35%" alt="">
            <h5 class="card-title text-uppercase text-center mt-2">Gary</h5>
            <h6 class="text-primary my-1">CEO & Foundator</h6>
            <hr>
            <p class="mt-5 px-5">He is known to be the best in the business !

            </p>
            <div class="btn-follow position-absolute mx-auto">
              <button class="border-0 rounded bg-transparent" style="font-size: 26px;"><i class="fab fa-linkedin"></i></button>
              <button class="border-0 rounded bg-transparent" style="font-size: 26px;"><i class="fab fa-github-square"></i></button>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 mt-4">
        <div class="card mb-5 mb-lg-0">
          <div class="card-body mt-n5 pt-0">
            <img src="images/avatars/2020.04.01.15.37.36-git.png" height="35%" width="35%" alt="">
            <h5 class="card-title text-uppercase text-center mt-2">Tim</h5>
            <h6 class="text-primary my-1">Foundator</h6>
            <hr>
            <p class="mt-5 px-5">As virtual colleague goes, noone is half as good as him !
            </p>
            <div class="btn-follow position-absolute mx-auto">
              <button class="border-0 rounded bg-transparent" style="font-size: 26px;"><i class="fab fa-linkedin"></i></button>
              <button class="border-0 rounded bg-transparent" style="font-size: 26px;"><i class="fab fa-github-square"></i></button>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 mt-4">
        <div class="card mb-5 mb-lg-0">
          <div class="card-body mt-n5 pt-0">
            <img src="images/avatars/default_profile.png" alt="">
            <h5 class="card-title text-uppercase text-center mt-2">Default</h5>
            <h6 class="text-primary my-1">Generator</h6>
            <hr>
            <p class="mt-5 px-5">As useless fake user goes.
              He is an important member of the team !
            </p>
            <div class="btn-follow position-absolute mx-auto">
              <button class="border-0 rounded bg-transparent" style="font-size: 26px;"><i class="fab fa-linkedin"></i></button>
              <button class="border-0 rounded bg-transparent" style="font-size: 26px;"><i class="fab fa-github-square"></i></button>
            </div>
          </div>
        </div>
      </div>
    
  </div>
</div>
<?php include 'partials/footer.php' ?>
<?php include 'partials/end.php' ?>