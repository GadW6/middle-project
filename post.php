<?php

session_start();
session_regenerate_id();
require_once 'utils/helpers.php';
$page_title = 'Post';

if (!auth_user()) {
  header('location: blog_sm.php');
  die;
} 

// Handles Output Post
$postId = $_GET['q'];
$userId = $_SESSION['user_id'];
$mysql = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE, DB_PORT);
$sql = "SELECT u.name, u.id, ua.avatar_name, p.title, p.image_upload, p.image_url, p.header, p.text, p.id AS 'post_id', p.date FROM blogg.posts p JOIN users u ON u.id = p.user_id JOIN users_avatar ua ON ua.user_id = p.user_id WHERE p.id='$postId';";
$result = mysqli_query($mysql, $sql);
$post = mysqli_fetch_assoc($result);

// Handles Input Comments
if (isset($_POST['comment-body'])) {
  $body = filter_input(INPUT_POST, 'comment-body', FILTER_SANITIZE_STRING);
  $body = trim($body);
  $form_valid = true;
  if (!$body || strlen($body) > 1500) {
    $form_valid = false;
  }
  if ($form_valid) {
    $body = mysqli_real_escape_string($mysql, $body);
    $now = date("Y-m-d H:i:s");
    $sql = "INSERT INTO comments VALUES(null, '$postId', '$userId', '$body', '$now');";
    $commentPost = mysqli_query($mysql, $sql);
    if ($commentPost && mysqli_affected_rows($mysql) > 0) {
      header('location: post.php?q=' . $postId);
      exit;
    }
  }
}

// Handles Output Comments
$sql = "SELECT ua.avatar_name, c.*, u.name 
FROM comments c 
JOIN users u ON u.id = c.user_id 
JOIN users_avatar ua ON ua.user_id = c.user_id
JOIN posts p ON p.id = c.id_post
WHERE p.id = '$postId';";
$commentShow = mysqli_query($mysql, $sql);

// Handles Input LikeBtn
if(isset($_POST['likeAction'])) {
  $sql = "SELECT * FROM comment_love WHERE user_id='$userId' AND post_id='$postId';";
  $result = mysqli_query($mysql, $sql);
  if($result && mysqli_num_rows($result) > 0){
    $likeStatus = true;
    $sql = "DELETE FROM comment_love WHERE user_id='$userId' AND post_id='$postId';";
    mysqli_query($mysql, $sql);  
  } else {
    $likeStatus = false;
    $sql = "INSERT INTO comment_love VALUES(null, '$userId', '$postId');";
    mysqli_query($mysql, $sql);
  }
  header('location: post.php?q=' . $postId);
  exit;
}

// Handles Output LikeBtn(Num & Color Status)
$commentLoveSql = "SELECT * FROM comment_love WHERE post_id='$postId';";
$commentLoveResult = mysqli_query($mysql, $commentLoveSql);
$commentLoveStatus = "SELECT * FROM comment_love WHERE user_id='$userId' AND post_id='$postId';";
$commentLoveStatusResult = mysqli_query($mysql, $commentLoveStatus);
// var_dump(mysqli_num_rows($commentLoveStatusResult));

?>

<?php include 'partials/header.php' ?>
  <link rel="stylesheet" href="public/css/blog.css">
<?php include 'partials/navbar.php' ?>

  <!-- Start Blog -->
<div class="row blog-item mb-4 mx-auto">
  <div class="col-lg-8 col-11 mt-5 mb-3 mx-auto px-0">
      <a class="btn btn-primary" href="blog.php"><i class="fas fa-backward mr-3"></i>Back to blogs</a>
      <div class="card-columns" style="column-count: 1 !important; -webkit-column-count: 1 !important;">
      <!-- Post Content Column -->
        <div class="card bg-transparent border-0 my-2">
          <div class="border border-primary rounded py-3 px-5 d-inline-block post w-100">
            <h1 class="mt-4"><?= $post['title'] ?></h1>
            <section >
              <img class="d-inline-block" height="35px" width="35px" src="/images/avatars/<?= $post['avatar_name'] ?>" alt="" srcset="">
              <a class="d-inline-block lead align-middle ml-2" href="/profile.php?q=<?= $post['id']; ?>"><?= ($_SESSION['user_id'] == $post['id']) ? 'Me' : $post['name'] ?></a>
            </section>
            <hr>
            <p class="pdate">Posted on <?= date('F d, Y', strtotime(explode(' ', $post['date'])[0]));  ?> at <?= date('H:i', strtotime(explode(' ', $post['date'])[1])); ?></p>
            <?php if($post['image_upload'] | $post['image_url']) : ?>
              <hr>
              <img class="img-fluid rounded mx-auto d-flex justify-content-center" src="<?= ($post['image_url']) ? $post['image_url'] : "/images/posts/" . $post['image_upload']; ?>" alt="image of post"> 
            <?php endif; ?>
            <?php if($post['header']) : ?>
              <hr>
              <p class="lead"><u class="border-bottom border-secondary" style="text-decoration: none !important;"><?= $post['header']; ?></u></p>
            <?php endif; ?>
            <div class="pb-3"><?= htmlspecialchars_decode($post['text'], ENT_HTML5); ?></div>
            <div class="footer-card">
              <div class="footer-card__left float-left my-2" style="color: black;">
                <form action="" method="POST" class="d-inline mr-2">
                  <input type="submit" name="likeAction" style="height: 1rem; width: 2rem; z-index:1000;" class="bg-transparent border-0 loveIcn position-absolute mt-1" value="">
                  <span id="love-count"><?= mysqli_num_rows($commentLoveResult); ?>
                  <?php if(mysqli_num_rows($commentLoveStatusResult) > 0) : ?>
                    <i class="fas fa-heart text-danger"></i>
                    <?php else : ?>
                    <i class="far fa-heart"></i>
                  <?php endif; ?>
                </span>
                </form>
                <button class="bg-transparent border-0 mr-2 commentIcn"><span id="comment-count"><?= mysqli_num_rows($commentShow); ?></span> <i class="far fa-comment"></i></button>
                <button type="button" class="bg-transparent border-0 linkIcn" data-toggle="tooltip" data-placement="bottom" data-original-title="Copy link to clipboard"><i class="fas fa-share-alt"></i></button>
              </div>
              <?php if($_SESSION['user_id'] == $post['id']) : ?>
              <div class="footer-card__right float-right my-2">
                <div class="btn-group dropup">
                  <button type="button" class="bg-transparent border-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                  </button>
                  <div class="dropdown-menu" style="width: 200px !important">
                    <a class="dropdown-item px-4" href="/edit_post.php?q=<?= $post['post_id']; ?>">Edit Post<span class="float-right"><i class="fas fa-pen"></i></span></a>
                    <button class="dropdown-item px-4" onclick="popModal('<?= $post['post_id']; ?>', '<?= ($post['image_upload']) ? $post['image_upload'] : ''; ?>')">Remove Post<span class="float-right text-danger"><i class="fas fa-trash"></i></span></a>
                  </div>
                </div>
              </div>
              <?php endif; ?>
            </div>
          </div>

            <!-- Comments Form -->
            <div class="card my-2 border-0 d-none" id="comment-section">
              <!-- Single Comment -->
              <?php if($commentShow && mysqli_num_rows($commentShow) > 0) : ?>
              <?php while($comments = mysqli_fetch_assoc($commentShow)) : ?>
              <div class="media p-3 d-flex comment-output">
                <img class="d-flex mr-3 rounded-circle" src="<?= 'images/avatars/' . $comments['avatar_name']; ?>" alt="" width="25px" height="25px">
                <div class="media-body">
                  <h5 class="mt-0"><?= ($_SESSION['user_id'] == $comments['user_id']) ? 'Me' : $comments['name'] ?><span class="float-right text-muted mr-3 my-auto" style="font-size: 0.8rem; line-height: 1.5rem;"><?= date('F d, Y', strtotime(explode(' ', $comments['date'])[0]));  ?> at <?= date('H:i', strtotime(explode(' ', $comments['date'])[1])); ?></span></h5>
                  <?= $comments['body'] ?>
                  <div class="footer-card mt-1">
                    <div class="footer-card__left float-left">
                      <button class="bg-transparent border-0 mr-2 loveIcn"><span id="love-count">0</span> <i class="far fa-heart"></i></button>
                      <button class="bg-transparent border-0 mr-2 commentIcn"><span id="comment-count">0</span> <i class="far fa-comment"></i></button>
                    </div>
                    <?php if($_SESSION['user_id'] == $comments['user_id']) : ?>
                    <div class="footer-card__right float-right mr-3">
                      <button class="bg-transparent text-dark border-0 delete-comment">
                        <i class="fas fa-trash"></i>
                      </button>
                      <aside class="d-none"><?= $comments['id'] ?></aside>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <?php endwhile; ?>
              <?php endif; ?>
              <!-- Comment Input Box -->
              <h5 class="card-header">Leave a Comment:</h5>
              <div class="card-body comment-input">
                <form action="" method="POST">
                  <div class="form-group">
                    <textarea name="comment-body" class="form-control border-secondary text-dark" rows="3"></textarea>
                  </div>
                  <input type="submit" class="btn btn-secondary px-5" value="submit"></input>
                </form>
              </div>
            </div>
            <!-- End Comment Section -->
            
          </div>
        </div>
    </div>
  </div>

<!-- Modal -->
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
        <p>By clicking on 'Remove the post' the current post will be deleted from publication.</p>
        <p>Otherwise clicking on 'Back' will get you back to the main wall.
        </p>
      </div>
      <div class="modal-footer w-100">
        <a href="" class="btn btn-outline-danger w-75 mx-0 cancel-modal">Remove the post</a>
        <button type="button" class="btn btn-outline-primary px-3 mx-auto close-modal" data-dismiss="modal">Back</button>
      </div>
    </div>
  </div>
</div>

  <!-- End Blog -->

<?php include 'partials/footer.php' ?>
<script>
  // Activate Tooltips
  $(document).ready(function() {
    $("body").tooltip({ selector: '[data-toggle=tooltip]' });
  });

  // Onclick Func
  function popModal(link, image) {
    document.querySelector('div.modal').classList.remove('d-none')
    document.querySelector('div.modal').classList.add('d-flex')
    if (image) {
      document.querySelector('.modal-footer a.cancel-modal').href = `delete_post.php?q=${link}&i=${image}`
    } else {
      document.querySelector('.modal-footer a.cancel-modal').href = `delete_post.php?q=${link}`
    }
  } 

  document.querySelectorAll('.close-modal').forEach(x => {
    x.addEventListener('click', (e) => {
      if (document.querySelector('div.modal').classList.contains('d-flex')) {
        document.querySelector('div.modal').classList.remove('d-flex')
        document.querySelector('div.modal').classList.add('d-none')
      }
      
      e.preventDefault()
    })
  })

  document.querySelector('button.commentIcn').addEventListener('click', (e) => {
    document.querySelector('#comment-section').classList.toggle('d-block')
    document.querySelector('#comment-section').scrollIntoView()

    e.preventDefault()
  })

  document.querySelectorAll('div.footer-card__right button.delete-comment').forEach(icn => {
    icn.addEventListener('click', (e) => {
      const comtId = icn.nextElementSibling.textContent
      window.location.replace(`delete_comment.php?q=${comtId}&p=${window.location.href.split('?q=')[1]}`)
      e.preventDefault()
    })
  })
  
</script>
<?php include 'partials/end.php' ?>