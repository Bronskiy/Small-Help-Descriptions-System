<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
  <meta name="generator" content="Jekyll v3.8.5">
  <!-- I know about SEO but I didn't added these features yet -->
  <title>Dashboard Template · Bootstrap</title>

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
  <!-- Custom styles for this template -->
  <link href="/assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Hi, <?php echo $_SESSION['username']; ?></a>
    <ul class="navbar-nav px-3">
      <li class="nav-item text-nowrap">
        <a class="nav-link" href="/help/sign-out">Sign out</a>
      </li>
    </ul>
  </nav>
  <?php
  $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $uri_segments = explode('/', $uri_path);
  ?>
  <div class="container-fluid">
    <div class="row">
      <nav class="col-md-2 d-none d-md-block bg-light sidebar">
        <div class="sidebar-sticky">
          <ul class="nav flex-column">
            <?php if ($_SESSION['role'] == 2 ) { ?>
            <li class="nav-item">
              <a class="nav-link <?php echo ($uri_segments[2] === 'descriptions' ? 'active' : ''); ?>" href="/help/descriptions">
                <span data-feather=""></span>
                Descriptions
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link <?php echo ($uri_segments[2] === 'add-desc' ? 'active' : ''); ?>" href="/help/add-desc">
                <span data-feather="add-desc"></span>
                Add Description
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php echo ($uri_segments[2] === 'users' ? 'active' : ''); ?>" href="/help/users">
                <span data-feather="users"></span>
                Users
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link <?php echo ($uri_segments[2] === 'add-user' ? 'active' : ''); ?>" href="/help/add-user">
                <span data-feather="add-user"></span>
                Add User
              </a>
            </li>
          <?php } ?>

          </ul>
        </div>
      </nav>
      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
