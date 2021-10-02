<?php

session_start();
session_regenerate_id();
require_once 'utils/helpers.php';

if (auth_user()) {
  header('location: ./');
  exit;
}

$page_title = 'Register';
$errors = [];

if (isset($_POST['submit'])) {
  if (isset($_SESSION['csrf_token']) && isset($_POST['csrf_token']) && $_SESSION['csrf_token'] == $_POST['csrf_token']) {
    $mysql = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE, DB_PORT);
    
    $uuid = uniqid();

    $name = filter_input(INPUT_POST, 'input-name', FILTER_SANITIZE_STRING);
    $name = trim($name);
    $name = mysqli_real_escape_string($mysql, $name);

    $email = filter_input(INPUT_POST, 'input-email', FILTER_VALIDATE_EMAIL);
    $email = trim($email);
    $email = mysqli_real_escape_string($mysql, $email);

    $password = filter_input(INPUT_POST, 'input-password', FILTER_SANITIZE_STRING);
    $password = trim($password);
    $password = mysqli_real_escape_string($mysql, $password);

    $password2 = filter_input(INPUT_POST, 'input-conf-pass', FILTER_SANITIZE_STRING);
    $password2 = trim($password2);
    $password2 = mysqli_real_escape_string($mysql, $password2);

    $form_valid = true;
    $image_name = 'default_profile.png';

    if (!$name || strlen($name) < 2 || strlen($name) > 100) {
      $form_valid = false;
      array_push($errors, 'The name is not valid. It needs to be between 2 an 100 characters..');
    }

    if (!$email) {
      $form_valid = false;
      array_push($errors, 'The email is not valid..');
    } elseif (email_exist($email, $mysql)) {
      $form_valid = false;
      array_push($errors, "Email '$email' is already taken..");
    }

    if (!$password || strlen($password) < 8 || strlen($password) > 20) {
      $form_valid = false;
      array_push($errors, 'The password is not valid. It needs to be between 8 and 20 characters..');
    }

    if ($password !== $password2) {
      $form_valid = false;
      array_push($errors, 'The confirmation password is not valid. Make sure both passwords match..');
    }

    if ($form_valid && isset($_FILES['file-upload-field']['error']) && $_FILES['file-upload-field']['error'] == 0) {

      if (check_avatar($_FILES)) {
        $image_name = date('Y.m.d.H.i.s') . '-' . $_FILES['file-upload-field']['name'];
        move_uploaded_file($_FILES['file-upload-field']['tmp_name'], 'images/avatars/' . $image_name);
      } else {
        $form_valid = false;
        array_push($errors, 'The valid formats are: jpeg/jpg/png/bmp/gif and the max. size is: 5mb');
      }
    }

    if ($form_valid) {
      $password = password_hash($password, PASSWORD_BCRYPT);
      $sql = "INSERT INTO users VALUES('$uuid', '$name', '$email', '$password')";
      $result = mysqli_query($mysql, $sql);

      if ($result && mysqli_affected_rows($mysql) > 0) {
        $sql = "INSERT INTO users_avatar VALUES(null, '$uuid', '$image_name')";
        $result = mysqli_query($mysql, $sql);
        if ($result &&  mysqli_affected_rows($mysql) > 0) {
          $_SESSION['user_id'] = $uuid;
          $_SESSION['user_name'] = $name;
          $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
          $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
          header('location: blog.php');
          exit;
        }
      }
    }
  }
  $token = csrf_token();
} else {
  $token = csrf_token();
}

?>

<?php include 'partials/header.php' ?>
<link rel="stylesheet" href="public/css/form.css">
<?php include 'partials/navbar.php' ?>
<div class="row">
  <div class="login-card col-lg-6 mx-auto p-5">
    <form action="" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= $token; ?>">
      <h1 class="text-center border-1 border-bottom border-primary pb-3 mb-3">Register</h1>
      <?php if ($errors) : ?>
        <div class="bg-transparent alert alert-dismissible alert-danger" style="color: #f5c6cb;">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong style="color: #f5c6cb;">Something went wrong !</strong> 
          <?php foreach($errors as $error): ?>
            <?php if(count($errors) === 1) : ?>
              <br/><?= $error; ?>
            <?php else : ?>
              <br/>- <?= $error; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <div class="form-group">
        <label for="input-name">Name</label>
        <input type="text" name="input-name" class="form-control" value ="<?= old('input-name'); ?>" id="input-name" aria-describedby="input name" placeholder="Enter name" autofocus="autofocus">
      </div>
      <div class="form-group">
        <label for="input-email">Email address</label>
        <input type="email" name="input-email" class="form-control" value="<?= old('input-email'); ?>" id="input-email" aria-describedby="input email" placeholder="Enter email">
      </div>
      <div class="form-group">
        <label for="input-password">Password</label>
        <input type="password" name="input-password" class="form-control" id="input-password" aria-describedby="input password" placeholder="Enter password">
      </div>
      <div class="form-group">
        <label for="input-conf-pass">Confirm Password</label>
        <input type="password" name="input-conf-pass" class="form-control" id="input-conf-pass" placeholder="Confirm password">
      </div>
      <div class="form-group mb-4" id="formUpload">
        <label for="file-upload-field">Upload your avatar <span class="small text-muted">(Optional. Computer only)</span></label>
        <div class="file-upload-wrapper" data-text="Choose image">
          <input name="file-upload-field" type="file" class="file-upload-field" value="">
        </div>
      </div>
      <hr>
      <input class="btn text-light mt-2" type="submit" name="submit" value="Register">
    </form>
  </div>
</div>
<?php include 'partials/footer.php' ?>
  <script src="public/js/register.js"></script>
<?php include 'partials/end.php' ?>