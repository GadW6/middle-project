</head>
<body> 
 <div class="container-fluid main-container">
  <nav class="navbar navbar-expand-lg navbar-trans navbar-light navbar-inverse border-bottom py-3 px-md-5 px-3">
    <a class="navbar-brand font-weight-bold" href="/"> <img src="public/css/src/blogg-logo.png" width="100vw" style="margin-bottom: 5px; margin-right: 1px;"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
      <i class="fas fa-bars"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor02">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item mr-4 <?= ($_SERVER['REQUEST_URI'] == '/') ? 'active' : ''; ?>">
          <a class="nav-link under-line" href="/">Home</a>
        </li>
        <li class="nav-item mr-4 <?= ($_SERVER['REQUEST_URI'] == '/blog.php') ? 'active' : ''; ?>">
          <a class="nav-link under-line" href="/blog.php">Blogg</a>
        </li>
        <li class="nav-item mr-4 <?= ($_SERVER['REQUEST_URI'] == '/about.php') ? 'active' : ''; ?>">
          <a class="nav-link under-line" href="/about.php">About</a>
        </li>
      </ul>
      <div class="btn-group" role="group">
        <?php if(!auth_user()) : ?>
          <a href="/login.php" class="color-white btn btn-outline-secondary border-0">Login</a>
          <a href="/register.php" class="color-white btn btn-outline-primary border-0">Register</a>
        <?php else : ?>
          <a href="<?= '/profile.php?q=' . $_SESSION['user_id'] ?>" class="color-white btn btn-outline-secondary border-0"><?= htmlentities($_SESSION['user_name']); ?></a>
          <a href="/logout.php" class="color-white btn btn-outline-primary border-0">Logout</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>