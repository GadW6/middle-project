<?php

session_start();
session_regenerate_id();
require_once 'utils/helpers.php';
$page_title = 'Login';

if (auth_user()) {
  header('location: ./');
}

$errors = [];

if (isset($_POST['submit'])) {
  if (isset($_SESSION['csrf_token']) && isset($_POST['csrf_token']) && $_SESSION['csrf_token'] === $_POST['csrf_token']) {
    
    $email = filter_input(INPUT_POST, 'input-email', FILTER_VALIDATE_EMAIL);
    $email = trim($email);
    
    $password = filter_input(INPUT_POST, 'input-password', FILTER_SANITIZE_STRING);
    $password = trim($password);
    
    if (!$email) {
      array_push($errors, 'A valid email is required..');
    }
    
    if (!$password) {
      array_push($errors, 'A valid password is required..');
    } 
    
    else {
      $mysql = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE, DB_PORT);
      $email = mysqli_real_escape_string($mysql, $email);
      $password = mysqli_real_escape_string($mysql, $password);
      $sql = "SELECT * FROM users WHERE email = '$email'";
      $result = mysqli_query($mysql, $sql);

      if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);      
        
        if (password_verify($password, $user['password'])) {
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['user_name'] = $user['name'];
          $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
          $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
          header('location: blog.php');
          exit;
        }

        else {
          array_push($errors, 'Wrong credentials. Try again..');
        }
      }

      else {
        array_push($errors, 'Wrong credentials. Try again..');
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
    <form action="" method="POST" novalidate autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= $token; ?>">
      <h1 class="text-center border-1 border-bottom border-primary pb-3 mb-3">Sign in</h1>
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
        <label for="input-email">Email address</label>
        <input type="email" class="form-control" id="input-email" aria-describedby="input email" placeholder="Enter email" name="input-email" value="<?= old('input-email'); ?>" autofocus="autofocus">
        <small class="form-text text-muted" style="color: #87919a !important">We do not share your email</small>
      </div>
      <div class="form-group mb-4">
        <label for="input-password">Password</label>
        <input type="password" class="form-control" id="input-password" placeholder="Password" name="input-password">
      </div>
      <hr>
      <input class="btn text-light mt-2" type="submit" name="submit" value="Signin">
    </form>
  </div>
</div>
<?php include 'partials/footer.php' ?>
<?php include 'partials/end.php' ?>