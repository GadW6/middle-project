<?php

session_start();
session_regenerate_id();
require_once 'utils/helpers.php';
$page_title = 'Add Post';

if (!auth_user()) {
  header('location: login.php');
  die;
}

$errors = [];

if (isset($_POST['submit'])) {

  $title = filter_input(INPUT_POST, 'input-title', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $title = trim($title);

  if ($_POST['input-url']) {
    $imageURL = filter_input(INPUT_POST, 'input-url', FILTER_VALIDATE_URL);
    if ($imageURL) {
      $imageURL = trim($imageURL);
    } else {
      $errorUrl = true;
    }
  }

  if ($_POST['input-header']) {
    $header = filter_input(INPUT_POST, 'input-header', FILTER_SANITIZE_STRING);
    $header = trim($header);
    if (!$header || strlen($header) < 5) {
      $errorHeader = true;
    }
  }

  $article = filter_input(INPUT_POST, 'input-body', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $article = trim($article);

  $form_valid = true;

  if (!$title || strlen($title) < 3) {
    $form_valid = false;
    array_push($errors, 'The Main Title field is invalid! The field is mandatory and must be above 3 chararcters..');
  }

  if ($_POST['input-url'] && $_FILES['input-upload']) {
    if (!$errorUrl && $_FILES['input-upload']['error'] == 0) {
      $form_valid = false;
      array_push($errors, 'Both URL and Upload are filled. Pick only one..');
    } else if ($errorUrl && $_FILES['input-upload']['error'] == 0) {
      $form_valid = true;
      $imageURL = null;
    } else if (!$errorUrl && $_FILES['input-upload']['error'] > 0){
      $form_valid = true;
    } else if ($errorUrl && $_FILES['input-upload']['error'] > 0) {
      $form_valid = false;
      array_push($errors, 'The URL is invalid! Try again..');
    }
  } 
  
  if ($errorHeader) {
    $form_valid = false;
    array_push($errors, 'The Header is invalid! It must be above 5 characters..');
  }
  
  if (!$article || strlen($article) < 10) {
    $form_valid = false;
    array_push($errors, 'The Article field is invalid! The field is mandator and must be above 10 characters..');
  }
  
  if ($form_valid && isset($_FILES['input-upload']['error']) && $_FILES['input-upload']['error'] == 0) {
    if (check_image_post($_FILES)) {
      $image_name = $_SESSION['user_id'] . '-*-' . date('Y.m.d.H.i.s') . '-*-' . $_FILES['input-upload']['name'];
      move_uploaded_file($_FILES['input-upload']['tmp_name'], 'images/posts/' . $image_name);
    } else {
      $form_valid = false;
      array_push($errors, 'The image uploaded is not valid! The valid formats are: jpeg/jpg/png/bmp/gif and the max. size is: 5mb');
    }
  } else if ($form_valid && !$_FILES['input-upload']) {
    $image_name = null;
  }

  if ($form_valid) {
    $uuid = uniqid(uniqid());
    $userId = $_SESSION['user_id'];
    $mysql = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE, DB_PORT);
    $title = mysqli_real_escape_string($mysql, $title);
    $header = mysqli_real_escape_string($mysql, $header);
    $article = mysqli_real_escape_string($mysql, $article);
    $now = date("Y-m-d H:i:s");
    $sql = "INSERT INTO posts VALUES('$uuid', '$userId', '$title', '$image_name', '$imageURL', '$header', '$article', '$now')";
    $result = mysqli_query($mysql, $sql);

    if ($result && mysqli_affected_rows($mysql) > 0) {
      header('location: blog.php');
      exit;
    }

  }
}

?>

<?php include 'partials/header.php' ?>
  <link rel="stylesheet" href="public/css/blog.css">
<?php include 'partials/navbar.php' ?>

  <!-- Start Blog -->
<div class="row blog-item mb-2 mx-auto">
  <div class="col-lg-8 col-11 mt-5 mb-3 mx-auto px-0">
    
    <!-- Post Content Column -->
    <div class="border border-primary rounded py-3 px-md-5 px-3 d-block post">
      <form action="" method="POST" id="add-post" novalidate="novalidate" autocomplete="off" enctype="multipart/form-data">
        <h1 class="display-4 mx-auto mt-3 mb-5 py-2 text-primary text-center border-primary border-bottom w-75">Add Your Post:</h1>
        <?php if ($errors) : ?>
          <div class="text-danger border-danger alert alert-dismissible alert-danger p-4">
            <button type="button" style="color: red;" class="close" data-dismiss="alert">&times;</button>
            <strong">Something went wrong !</strong"> 
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
          <label class="col-form-label col-form-label-lg ml-1" for="input-title">Main Title</label>
          <input type="text" class="form-control form-control-lg" placeholder="ex: 'Tesla'" id="input-title" name="input-title" autofocus="autofocus">
        </div>
        <div class="form-group">
          <label class="d-block col-form-label ml-1" for="input-header">Set URL or Upload the post's picture <small class="text-muted">(Optional)</small></label>
          <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
            <label class="btn btn-secondary">
              <input type="radio" id="btn-url" checked> URL
            </label>
            <label class="btn btn-secondary active">
              <input type="radio" id="btn-upload"> Upload
            </label>
          </div>
        </div>
        <div class="form-group mt-n2" id="field-url">
          <div class="form-group">
            <div class="input-group mb-3">
              <input type="text" class="form-control" aria-label="insert url" placeholder="ex: http://randonwebsite.com/image.png" name="input-url">
              <div class="input-group-append">
                <span class="input-group-text">URL</span>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group mt-n2 d-none" id="field-upload">
          <div class="input-group">
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="field-upload-element" name="input-upload">
              <label class="custom-file-label" for="field-upload-element">Upload image..</label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-form-label ml-1" for="input-header">Header <small class="text-muted">(Sub-title of the post. Optional)</small></label>
          <input type="text" class="form-control" placeholder="ex: 'My adventures in a electric car'" id="input-header" name="input-header">
        </div>
        <div class="form-group">
          <label class="col-form-label ml-1" for="input-body">Article Text:</label>
          <div class="form-control"style="min-height: 45vh; font-family: sans-serif;" data-tiny-editor data-formatblock="no" data-fontname="no" data-justifyleft="no" data-justifycenter="no" data-justifyright="no" data-outdent="no" data-indent="no" data-remove-format="no">
          </div>
          <textarea class="d-none" name="input-body" id="input-body" cols="0" rows="0"></textarea>
        </div>
        <div class="btn-group w-100 my-3">
          <input type="submit" name="submit" class="d-inline-block col-md-8 btn btn-lg btn-success" value="Save the post">
          <button type="button" class="col-md-4 btn btn-lg btn-danger" id="cancel-btn">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

  <!-- Adding Modals -->
<div class="modal d-none align-items-center" style="background-color: rgba(0, 0, 0, 0.35)">
  <div class="modal-dialog" role="document">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title" style="color: red">Are You Sure ?</h5>
        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="close-modal">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>By clicking on 'Cancel the new post' you will be redirected back to the blog menu and your new post and all the changes made to it will not be saved !</p>
        <p>Otherwise clicking on 'Back' will get you back to your new post.
        </p>
      </div>
      <div class="modal-footer w-100">
        <button type="button" class="btn btn-outline-danger w-75 mx-0 cancel-modal">Cancel the new post</button>
        <button type="button" class="btn btn-outline-primary px-3 mx-auto close-modal" data-dismiss="modal">Back</button>
      </div>
    </div>
  </div>
</div>

  <!-- End Blog -->

<?php include 'partials/footer.php' ?>
  <script src="https://unpkg.com/tiny-editor/dist/bundle.js"></script>
  <script src="public/js/add-post.js"></script>
<?php include 'partials/end.php' ?>