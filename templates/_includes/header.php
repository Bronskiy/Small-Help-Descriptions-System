<?php
$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', $uri_path);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
  <meta name="generator" content="Jekyll v3.8.5">
  <!-- I know about SEO but I didn't added these features yet -->
  <title>Dieprojektsoftware Help Center</title>

  <!-- Bootstrap core CSS -->
  <link href="/assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <style>
  .bd-placeholder-img {
    font-size: 1.125rem;
    text-anchor: middle;
  }

  @media (min-width: 768px) {
    .bd-placeholder-img-lg {
      font-size: 3.5rem;
    }
  }
  </style>
  <script async charset="utf-8" src="//cdn.embedly.com/widgets/platform.js"></script>
  <?php if ($uri_segments[1] === 'descriptions' || $uri_segments[1] === 'login') { ?>
    <script src="https://cdn.ckeditor.com/ckeditor5/12.1.0/classic/ckeditor.js"></script>
    <script src="/assets/lib/ckfinder/ckfinder.js"></script>
  <?php } ?>
  <link href="/assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
    <?php if ($_SESSION['username']) { ?>
      <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Hi, <?php echo $_SESSION['username']; ?></a>
    <?php } else { ?>
      <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Dieprojektsoftware</a>
    <?php } ?>
    <form class="w-100" action="/search" method="post">
      <input class="form-control form-control-dark w-100" name="keyword" type="text" placeholder="Search" aria-label="Search">
    </form>
    <ul class="navbar-nav px-3">
      <li class="nav-item text-nowrap">
        <?php if ($_SESSION['username']) { ?>
          <a class="nav-link" href="/sign-out">Sign out</a>
        <?php } else { ?>
          <a class="nav-link" href="/login">Login</a>
        <?php } ?>
      </li>
    </ul>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <nav class="col-md-2 d-none d-md-block bg-light sidebar">
        <div class="sidebar-sticky">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>Controls</span>
              <a class="d-flex align-items-center text-muted" href="#">
                <span data-feather="plus-circle"></span>
              </a>
            </h6>
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link <?php echo ($uri_segments[1] === 'descriptions' && !$uri_segments[2] ? 'active' : ''); ?>" href="/descriptions">
                  <span data-feather=""></span>
                  Descriptions
                </a>
              </li>

              <?php if ($_SESSION['uid']) { ?>
              <li class="nav-item">
                <a class="nav-link <?php echo ($uri_segments[1] === 'descriptions' && $uri_segments[2] === 'add' ? 'active' : ''); ?>" href="/descriptions/add">
                  <span data-feather="add-desc"></span>
                  Add Description
                </a>
              </li>
              <?php } ?>
              <?php if ($_SESSION['role'] == 2 ) { ?>
              <li class="nav-item">
                <a class="nav-link <?php echo ($uri_segments[1] === 'users' && !$uri_segments[2] ? 'active' : ''); ?>" href="/users">
                  <span data-feather="users"></span>
                  Users
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php echo ($uri_segments[1] === 'users' && $uri_segments[2] === 'add' ? 'active' : ''); ?>" href="/users/add">
                  <span data-feather="add-user"></span>
                  Add User
                </a>
              </li>
            <?php } ?>
            </ul>
          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Descriptions List</span>
            <a class="d-flex align-items-center text-muted" href="#">
              <span data-feather="plus-circle"></span>
            </a>
          </h6>
          <div id="desc_menu"></div>

        </div>
      </nav>
      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
