<?php

session_start();
session_regenerate_id();
require_once 'utils/helpers.php';
$page_title = 'Profile';

if (!auth_user()) {
  header('location: login.php');
  die;
} 

$userId = $_SESSION['user_id'];
$userDestId = $_GET['q'];

if (!$userDestId) {
  header('location: 403.php');
  die;
}

$mysql = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE, DB_PORT);
// Handles Output User
$sql = "SELECT u.*, ua.avatar_name, ext.about_me
FROM users u
JOIN users_avatar AS ua ON ua.user_id = u.id
LEFT JOIN users_ext AS ext ON ext.user_id = u.id
WHERE u.id = '$userDestId';";
$result = mysqli_query($mysql, $sql);
$User = mysqli_fetch_assoc($result);

// Handles Output Post
$sql = "SELECT u.id, p.title, p.date, p.id AS 'post_id'
FROM users u
JOIN posts AS p ON p.user_id = u.id
WHERE u.id = '$userDestId'
ORDER BY p.date DESC;";
$resultPost = mysqli_query($mysql, $sql);
$PostCount = mysqli_num_rows($resultPost);
// $Post = mysqli_fetch_assoc($result);

// Handles Output Comment
$sql = "SELECT u.id, c.body, c.date, c.id_post
FROM users u
JOIN comments AS c ON c.user_id = u.id
WHERE u.id = '$userDestId'
ORDER BY c.date DESC;";
$resultCom = mysqli_query($mysql, $sql);
$ComCount = mysqli_num_rows($resultCom);
// $Com = mysqli_fetch_assoc($result);

// Handles Input About Text
if (isset($_POST['about-submit']) && $userId == $userDestId) {
  $text = filter_input(INPUT_POST, 'text-profile', FILTER_SANITIZE_STRING);
  $text = trim($text);
  $text = mysqli_real_escape_string($mysql, $text);
  $sql = "UPDATE users_ext SET about_me = '$text' WHERE user_id = '$userId';";
  $result = mysqli_query($mysql, $sql);
  if ($result && mysqli_affected_rows($mysql) > 0) {
    header('location: /profile.php?q=' . $userId);
    exit;
  } else {
    $sql = "INSERT INTO users_ext VALUES(null, '$userId', '$text');";
    mysqli_query($mysql, $sql);
    header('location: /profile.php?q=' . $userId);
    exit;
  }
}

// Handles Input Profile Fields
$errors = [];
if (isset($_POST['profile-submit']) && $userId == $userDestId && isset($_SESSION['csrf_token']) && isset($_POST['csrf_token']) && $_SESSION['csrf_token'] == $_POST['csrf_token']) {
  $name = filter_input(INPUT_POST, 'input-name', FILTER_SANITIZE_STRING);
  $name = trim($name);
  $name = mysqli_real_escape_string($mysql, $name);
  $email = filter_input(INPUT_POST, 'input-email', FILTER_VALIDATE_EMAIL);
  $email = trim($email);
  $email = mysqli_real_escape_string($mysql, $email);
  $password = filter_input(INPUT_POST, 'input-pass', FILTER_SANITIZE_STRING);
  $password = trim($password);
  $password = mysqli_real_escape_string($mysql, $password);
  $password2 = filter_input(INPUT_POST, 'input-conf-pass', FILTER_SANITIZE_STRING);
  $password2 = trim($password2);
  $password2 = mysqli_real_escape_string($mysql, $password2);

  $form_valid = true;

  if (!$name || strlen($name) < 2 || strlen($name) > 100) {
    $form_valid = false;
    array_push($errors, 'The name is not valid. It needs to be between 2 an 100 characters..');
  }

  if ($email !== $User['email']) {
    if (!$email) {
      $form_valid = false;
      array_push($errors, 'The email is not valid..');
    } elseif (email_exist($email, $mysql)) {
      $form_valid = false;
      array_push($errors, "Email '$email' is already taken..");
    }
  }

  if (!$password || strlen($password) < 8 || strlen($password) > 20) {
    $form_valid = false;
    array_push($errors, 'The password is not valid. It needs to be between 8 and 20 characters..');
  }

  if ($password !== $password2) {
    $form_valid = false;
    array_push($errors, 'The confirmation password is not valid. Make sure both passwords match..');
  }

  if ($name == $User['name'] && $email == $User['email'] && $password == substr($User['password'], -19) && $_FILES['file-upload-field']['size'] == 0) {
    $form_valid = false;
  }

  if ($form_valid && isset($_FILES['file-upload-field']['error']) && $_FILES['file-upload-field']['error'] == 0) {
    if (check_avatar($_FILES)) {
      unlink('images/avatars/' . $User['avatar_name']);
      $image_name = date('Y.m.d.H.i.s') . '-' . $_FILES['file-upload-field']['name'];
      move_uploaded_file($_FILES['file-upload-field']['tmp_name'], 'images/avatars/' . $image_name);
      $sql = "UPDATE users_avatar SET avatar_name = '$image_name' WHERE user_id = '$userId';";
      mysqli_query($mysql, $sql);
    } else {
      $form_valid = false;
      array_push($errors, 'The valid formats are: jpeg/jpg/png/bmp/gif and the max. size is: 5mb');
    }
  }
  
  if ($form_valid) {
    if ($password !== $User['password']) {
      $password = password_hash($password, PASSWORD_BCRYPT);
      $sql = "UPDATE users SET name = '$name', email = '$email', password = '$password' WHERE id = '$userId';";
      $result = mysqli_query($mysql, $sql);
    } else {
      $sql = "UPDATE users SET name = '$name', email = '$email' WHERE id = '$userId';";
      $result = mysqli_query($mysql, $sql);
    }

    if ($result && mysqli_affected_rows($mysql) > 0) {
      $_SESSION['user_name'] = $name;
      header('location: profile.php?q=' . $userId);
      exit;
    }
  }
  $token = csrf_token();
} else {
  $token = csrf_token();
}

?>

<?php include 'partials/header.php' ?>
  <link rel="stylesheet" href="public/css/profile.css">
<?php include 'partials/navbar.php' ?>
<div class="container mt-5 px-1">
  <div class="row mx-0 text-light">
    <div class="col-md-4 px-1 bg-transparent my-1">
      <div class="container-fluid p-3 rounded">
        <img src="images/avatars/<?= $User['avatar_name']; ?>" width="40%" height="40%" class="rounded-circle my-3 mx-auto d-block" alt="image profile">
        <h3 class="text-center font-weight-bold"><?= ucfirst($User['name']); ?></h3>
        <p class="text-center font-italic text-muted"><?= $User['email']; ?></p>
        <div class="badges-container text-center my-4">
          <p class="m-0">Publications:</p>
          <span class="badge alert-warning text-dark mx-auto">Posts: <?= $PostCount; ?></span>
          <span class="badge alert-success text-dark mx-auto">Comments: <?= $ComCount; ?></span>
        </div>
        <nav>
          <div class="nav nav-tabs flex-column m-4 border-0" id="nav-tab" role="tablist">
            <a class="nav-item nav-link border-0 bg-transparent pl-4 active" id="nav-about-tab" data-toggle="tab" href="#nav-about" role="tab" aria-controls="nav-about" aria-selected="true">About Me</a>
            <?php if($userId == $userDestId) : ?>
            <a class="nav-item nav-link border-0 bg-transparent pl-4" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Profile</a>
            <?php endif; ?>
            <a class="nav-item nav-link border-0 bg-transparent pl-4" id="nav-public-tab" data-toggle="tab" href="#nav-public" role="tab" aria-controls="nav-public" aria-selected="false">Publications</a>
          </div>
        </nav>
      </div>
    </div>
    <div class="col-md-8 px-1 bg-transparent my-1">
      <div class="container-fluid p-md-5 p-3 rounded">
        <div class="tab-content mt-2" id="nav-tabContent">
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
          <div class="tab-pane fade show mb-n4 active" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
            <h1 class="text-center mb-3">About Me</h1>
            <form class="hidden" method="POST">
              <div class="pt-5 px-4 pb-4 d-block position-relative m-0 border">
                <textarea name="text-profile" style="width: 100%;" disabled><?= ($User['about_me']) ? $User['about_me'] : 'EX: 
                I am a person who is positive about every aspect of life. There are many things I like to do, to see, and to experience. I like to read, I like to write; I like to think, I like to dream; I like to talk, I like to listen. I like to see the sunrise in the morning, I like to see the moonlight at night; I like to feel the music flowing on my face, I like to smell the wind coming from the ocean...'; ?></textarea>
              </div>
              <?php if($userId == $userDestId) : ?>
              <button type="button" class="float-right position-relative text-white btn btn-lg py-1 px-3" id="editBtnAbout">edit</button>
              <button type="submit" class="float-right position-relative text-white btn btn-lg py-1 px-3" name="about-submit" id="saveBtnAbout">save</button>
              <?php endif; ?>
            </form>
          </div>
          <?php if($userId == $userDestId) : ?>
          <div class="tab-pane fade show mb-n4" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <h1 class="text-center mb-3">My Profile</h1>
            <form class="p-3 hidden" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
              <input type="hidden" name="csrf_token" value="<?= $token; ?>">
              <div class="pt-5 px-4 pb-4 d-block position-relative m-0 border">
                <div class="form-row">
                  <div class="form-group col-12">
                    <label for="inputName">Name:</label>
                    <input type="text" class="form-control" name="input-name" id="inputName" value="<?= $User['name']; ?>" disabled>
                  </div>
                  <div class="form-group col-12">
                    <label for="inputEmail">Email:</label>
                    <input type="text" class="form-control" name="input-email" id="inputEmail" value="<?= $User['email']; ?>" disabled>
                  </div>
                  <div class="form-group col-12" id="firstPass">
                    <label for="inputEmailConf">Password:</label>
                    <input type="password" class="form-control" name="input-pass" id="inputEmailConf" onfocus="this.value=''" value="<?= substr($User['password'], -19); ?>" disabled>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="inputPassword">Comfirm Password:</label>
                    <input type="password" class="form-control" name="input-conf-pass" id="inputPassword" onfocus="this.value=''" value="<?= substr($User['password'], -19); ?>" disabled>
                  </div>
                  <div class="form-group col-12 upload-form">
                    <label for="file-upload-field">Change your avatar's picture:</label>
                    <div class="input-group mb-3">
                      <div class="custom-file"> 
                        <input type="file" class="custom-file-input" name="file-upload-field" id="file-upload-field" disabled>
                        <label class="custom-file-label" for="file-upload-field">Choose file</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <button class="float-right position-relative text-white btn btn-lg py-1 px-3" type="button" id="editBtnProfile">edit</button>
              <button class="float-right position-relative text-white btn btn-lg py-1 px-3" name="profile-submit" type="submit" id="saveBtnProfile">save</button>
            </form>
          </div>
          <?php endif; ?>
          <div class="tab-pane fade show mb-n4 p-2" id="nav-public" role="tabpanel" aria-labelledby="nav-public-tab">
            <h1 class="text-center mb-4">My Publications:</h1>
            <h5 class="mt-3 font-weight-lighter">Posts Published:</h5>
            <table class="table table-hover">
              <thead>
                <tr class="table-active">
                  <th scope="col">Date</th>
                  <th scope="col">Post Title</th>
                  <th scope="col">Link</th>
                </tr>
              </thead>
              <tbody>
                <?php while($Post = mysqli_fetch_assoc($resultPost)) : ?>
                <tr>
                  <td><?= date('H:i d/m/Y', strtotime($Post['date'])); ?></td>
                  <td><?= htmlspecialchars_decode( (strlen($Post['title']) > 50) ? substr($Post['title'],0, 50) . '...' : $Post['title'] , ENT_HTML5); ?></td>
                  <td><a href="/post.php?q=<?= $Post['post_id']; ?>">View Post</a></td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
            <h5 class="mt-4 font-weight-lighter">Comments Published:</h5>
            <table class="table table-hover">
              <thead>
                <tr class="table-active">
                  <th scope="col">Date</th>
                  <th scope="col">Body Comment</th>
                  <th scope="col">Link</th>
                </tr>
              </thead>
              <tbody>
                <?php while($Com = mysqli_fetch_assoc($resultCom)) : ?>
                <tr>
                  <td><?= date('H:i d/m/Y', strtotime($Com['date'])); ?></td>
                  <td><?= htmlspecialchars_decode( (strlen($Com['body']) > 50) ? substr($Com['body'],0, 50) . '...' : $Com['body'] , ENT_HTML5); ?></td> 
                  <td><a href="/post.php?q=<?= $Com['id_post']; ?>">View Post</a></td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php' ?>
<script>
  // Textarea autoresize 
  $('textarea').each(function () {
    this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow:hidden;resize: none !important; width: 100%;');
  }).on('input', function () {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
  });

  // Toggle between display and edit state

  // Edit About Me
  const formAbout = document.querySelector('#nav-about form')
  const textarea = document.querySelector('#nav-about form textarea')
  const editAbout = document.querySelector('#nav-about #editBtnAbout')
  editAbout.addEventListener('click', e => {
    formAbout.classList.toggle('hidden')
    if (formAbout.classList.contains('hidden')) {
      editAbout.innerText = 'edit'
      while(!textarea.disabled) {textarea.disabled = true}
    } else {
      editAbout.innerText = 'back'
      while(textarea.disabled) {textarea.disabled = false}
    }
    e.preventDefault()
  })

  // Edit Profile
  const formProfile = document.querySelector('#nav-profile form')
  const inputs = document.querySelectorAll('#nav-profile form input')
  const inputPass = document.querySelector('#nav-profile form #firstPass')
  const editProfile = document.querySelector('#editBtnProfile')
  editProfile.addEventListener('click', e => {
    formProfile.classList.toggle('hidden')
    inputPass.classList.toggle('col-md-6')
    if(formProfile.classList.contains('hidden')){
      editProfile.innerText = 'edit'
      inputs.forEach(i => {
        while(!i.disabled) {i.disabled = true}
      })
    } else {
      editProfile.innerText = 'back'
      inputs.forEach(i => {
        while(i.disabled) {i.disabled = false}
      })
    }
    e.preventDefault()
  })
</script>
<?php include 'partials/end.php' ?>