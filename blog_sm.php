<?php

session_start();
session_regenerate_id();
require_once 'utils/helpers.php';
$page_title = 'Blog';

if (auth_user()) {
  header('location: blog.php');
  die;
}

function limit_text($text, $limit) {
  if (str_word_count($text, 0) > $limit) {
      $words = str_word_count($text, 2);
      $pos = array_keys($words);
      $text = substr($text, 0, $pos[$limit]);
  }
  return $text;
}

// Login

$errors = [];

if (isset($_POST['submit'])) {
  if (isset($_SESSION['csrf_token']) && isset($_POST['csrf_token']) && $_SESSION['csrf_token'] === $_POST['csrf_token']) {
    
    $email = filter_input(INPUT_POST, 'input-email', FILTER_VALIDATE_EMAIL);
    $email = trim($email);
    
    $password = filter_input(INPUT_POST, 'input-password', FILTER_SANITIZE_STRING);
    $password = trim($password);
    
    if (!$email | !$password) {
      header('location: login.php');
      die;
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

// Blog

$mysql = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE, DB_PORT);
$sql = "SELECT u.name, u.id, ua.avatar_name, p.title, p.image_upload, p.image_url, p.header, p.text, p.date FROM blogg.posts p JOIN users u ON u.id = p.user_id JOIN users_avatar ua ON ua.user_id = p.user_id ORDER BY p.date desc LIMIT 6;";
$result = mysqli_query($mysql, $sql);

?>

<?php include 'partials/header.php' ?>
  <link rel="stylesheet" href="public/css/form.css">
  <link rel="stylesheet" href="public/css/blog.css">
<?php include 'partials/navbar.php' ?>

  <!-- Start Blog -->
<div class="row blog-item mb-2 mx-auto mt-0">
  <div class="col-lg-8 col-11 mt-5 mb-3 mx-auto px-0">
    
  <div class="card-columns">
      <?php if ($result && mysqli_num_rows($result) > 0) : ?>
      <!-- Post Content Column -->
      <?php while($posts = mysqli_fetch_assoc($result)) : ?>
        <div class="card bg-transparent border-0 my-2">
          <div class="border border-primary rounded py-3 px-5 d-inline-block post w-100">
            <h1 class="mt-4"><?= $posts['title'] ?></h1>
            <section >
              <img class="d-inline-block" height="25px" width="25px" src="/images/avatars/<?= $posts['avatar_name'] ?>" alt="" srcset="">
              <a class="d-inline-block lead align-middle ml-1" href="#"><?= ($_SESSION['user_id'] == $posts['id']) ? 'Me' : $posts['name'] ?></a>
            </section>
            <hr>
            <p class="pdate">Posted on <?= date('F d, Y', strtotime(explode(' ', $posts['date'])[0]));  ?> at <?= date('H:i', strtotime(explode(' ', $posts['date'])[1])); ?></p>
            <?php if($posts['image_upload'] | $posts['image_url']) : ?>
              <hr>
              <img class="img-fluid rounded mx-auto d-flex justify-content-center" src="<?= ($posts['image_url']) ? $posts['image_url'] : "/images/posts/" . $posts['image_upload']; ?>" alt="image of post"> 
            <?php endif; ?>
            <?php if($posts['header']) : ?>
              <hr>
              <p class="lead"><u class="border-bottom border-secondary" style="text-decoration: none !important;"><?= $posts['header']; ?></u></p>
            <?php endif; ?>
            <div class="pb-3"><?= htmlspecialchars_decode(limit_text($posts['text'], 50) . '...</p><a href="#" class="readBtn">Read more</a>', ENT_HTML5); ?></div>
          </div>
        </div>
      <?php endwhile; ?>
      <?php endif; ?>
    </div>
    <div class="row modal d-none align-items-center m-0" id="modal-login" style="background-color: rgba(0, 0, 0, 0.65)">
      <div class="login-card col-lg-6 col-11 mx-auto p-5">
        <form action="" method="POST" novalidate autocomplete="off">
          <input type="hidden" name="csrf_token" value="<?= $token; ?>">
          <h1 class="text-center border-1 border-bottom border-primary pb-3 mb-3">Signin</h1>
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
            <input type="email" class="form-control" id="input-email" aria-describedby="input email" placeholder="Enter email" name="input-email" value="<?= old('input-email'); ?>">
            <small class="form-text text-muted" style="color: #87919a !important">We do not share your email</small>
          </div>
          <div class="form-group mb-4">
            <label for="input-password">Password</label>
            <input type="password" class="form-control" id="input-password" placeholder="Password" name="input-password">
          </div>
          <hr>
          <input class="btn text-light mt-2" type="submit" name="submit" value="Signin">
          <p class="d-block text-center my-2 text-muted">
            Not yet a member. <a class="py-3" href="register.php">Create account</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="text-center my-3">
  <button class="btn btn-lg btn-primary" id="btn-load">Load more</button>
</div>

  <!-- End Blog -->

<?php include 'partials/footer.php' ?>
<script>
  // Variable
  const modalLogin = document.querySelector('#modal-login');
  const btnLoad = document.querySelector('#btn-load');
  const readMoreBtn = document.querySelectorAll('a.readBtn');
  const profileBtn = document.querySelectorAll('section > a.lead')

  // Event Listeners
  btnLoad.addEventListener('click', (e) => {
    modalLogin.classList.remove('d-none');
    modalLogin.classList.add('d-flex');
    e.preventDefault();
  })

  readMoreBtn.forEach(btn => {
    btn.addEventListener('click', e => {
      modalLogin.classList.remove('d-none');
      modalLogin.classList.add('d-flex');
      e.preventDefault();
    })
  })

  profileBtn.forEach(btn => {
    btn.addEventListener('click', e => {
      modalLogin.classList.remove('d-none')
      modalLogin.classList.add('d-flex')
      e.preventDefault()
    })
  })

</script>
<?php include 'partials/end.php' ?>