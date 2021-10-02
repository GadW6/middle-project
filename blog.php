<?php

session_start();
session_regenerate_id();
require_once 'utils/helpers.php';
$page_title = 'Blog';

if (!auth_user()) {
  header('location: blog_sm.php');
  die;
} 

$mysql = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE, DB_PORT);
$sql = "SELECT u.name, u.id, ua.avatar_name, p.title, p.image_upload, p.image_url, p.header, p.text, p.id AS 'post_id', p.date FROM blogg.posts p JOIN users u ON u.id = p.user_id JOIN users_avatar ua ON ua.user_id = p.user_id ORDER BY p.date desc;";
$result = mysqli_query($mysql, $sql);

function limit_text($text, $limit) {
  if (str_word_count($text, 0) > $limit) {
      $words = str_word_count($text, 2);
      $pos = array_keys($words);
      $text = substr($text, 0, $pos[$limit]);
  }
  return $text;
}

?>

<?php include 'partials/header.php' ?>
  <link rel="stylesheet" href="public/css/blog.css">
<?php include 'partials/navbar.php' ?>

  <!-- Start Blog -->
<div class="row blog-item mb-4 mx-auto">
  <div class="col-lg-8 col-11 mt-5 mb-3 mx-auto px-0">
      <a href="add_post.php" class="btn btn-primary btn-block col col-md-3 ml-auto col-auto mb-2">Add Post +</a>

      <div class="card-columns">
      <?php if ($result && mysqli_num_rows($result) > 0) : ?>
      <!-- Post Content Column -->
      <?php while($posts = mysqli_fetch_assoc($result)) : ?>
        <div class="card bg-transparent border-0 my-2">
          <div class="border border-primary rounded py-3 px-5 d-inline-block post w-100">
            <h1 class="mt-4"><?= $posts['title'] ?></h1>
            <section >
              <img class="d-inline-block" height="35px" width="35px" src="/images/avatars/<?= $posts['avatar_name'] ?>" alt="" srcset="">
              <a class="d-inline-block lead align-middle ml-2" href="/profile.php?q=<?= $posts['id']; ?>"><?= ($_SESSION['user_id'] == $posts['id']) ? 'Me' : $posts['name'] ?></a>
            </section>
            <hr>
            <p class="pdate">Posted on <?= date('F d, Y', strtotime(explode(' ', $posts['date'])[0]));  ?> at <?= date('H:i', strtotime(explode(' ', $posts['date'])[1])); ?></p>
            <?php if($posts['image_upload'] | $posts['image_url']) : ?>
              <hr>
              <img class="img-fluid rounded mx-auto d-flex justify-content-center" src="<?= ($posts['image_url']) ? $posts['image_url'] : "/images/posts/" . $posts['image_upload']; ?>" alt="image of post"> 
              <?php endif; ?>
              <hr>
            <?php if($posts['header']) : ?>
              <p class="lead"><u class="border-bottom border-secondary" style="text-decoration: none !important;"><?= $posts['header']; ?></u></p>
            <?php endif; ?>
            <div class=""><?= htmlspecialchars_decode(limit_text($posts['text'], 50) . '...</p>', ENT_HTML5); ?></div>
            <div class="footer-card">
              <div class="footer-card__left float-left mb-2">
                <?= '<a href="/post.php?q=' . $posts['post_id'] . '" id="readBtn">Read more</a>'; ?>
              </div>
              <?php if($_SESSION['user_id'] == $posts['id']) : ?>
              <div class="footer-card__right float-right mb-2">
                <div class="btn-group dropup">
                  <button type="button" class="bg-transparent border-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                  </button>
                  <div class="dropdown-menu" style="width: 200px !important">
                    <a class="dropdown-item px-4" href="/edit_post.php?q=<?= $posts['post_id']; ?>">Edit Post<span class="float-right"><i class="fas fa-pen"></i></span></a>
                    <button class="dropdown-item px-4" onclick="popModal('<?= $posts['post_id']; ?>', '<?= ($posts['image_upload']) ? $posts['image_upload'] : ''; ?>')">Remove Post<span class="float-right text-danger"><i class="fas fa-trash"></i></span></button>
                  </div>
                </div>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
      <?php endif; ?>
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
  </script>
<?php include 'partials/end.php' ?>