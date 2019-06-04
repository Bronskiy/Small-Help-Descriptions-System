<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
  return $this->renderer->render($response, 'home.phtml', $args);
});

$app->post('/login', function (Request $request, Response $response) {
  $input = $request->getParsedBody();

  $sql = "SELECT * FROM users WHERE username = :inputLogin OR email = :inputLogin";
  $sth = $this->db->prepare($sql);
  $sth->bindParam("inputLogin", filter_var($input['inputLogin'], FILTER_SANITIZE_STRING));
  $sth->bindParam("inputPassword", $input['inputPassword']);
  $sth->execute();
  $user = $sth->fetchAll();

  if (password_verify($input['inputPassword'], $user[0]['password'])) {
    $session = $this->session;
    $session->set('uid', $user[0]['id']);
    $session->set('role', $user[0]['role_id']);
    $session->set('username', $user[0]['username']);
    return $response->withRedirect("/help/1");
  } else {
    // TODO: Wrong password message
    return $response->withRedirect("/help");
  }
});


$app->get('/users', function (Request $request, Response $response, array $args) {
  return $this->renderer->render($response, 'users.phtml', $args);
});

$app->get('/descriptions', function (Request $request, Response $response, array $args) {
  //return $this->renderer->render($response, 'home.phtml', $args);
});

$app->get('/add-user', function (Request $request, Response $response, array $args) {
  //return $this->renderer->render($response, 'home.phtml', $args);
});

$app->get('/add-desc', function (Request $request, Response $response, array $args) {
  //return $this->renderer->render($response, 'home.phtml', $args);
});


$app->get('/sign-out', function (Request $request, Response $response) {
  $session = $this->session;
  $session::destroy();
  return $response->withRedirect("/help");
});

$app->get('/{id}', function (Request $request, Response $response, array $args) {
  // Sample log message
  //$this->logger->info("Slim-Skeleton '/' route");

  // Render index view
/*
  if (isset($this->session->uid)) {
    return $response->withRedirect("/dashboard");
  } else {
    return $this->renderer->render($response, 'index.phtml', $args);
  }
  */
  if ($args['id'] != '') {
    $sql = "SELECT * FROM description WHERE id=:id";
    $sth = $this->db->prepare($sql);

    $sth->bindParam("id", $args['id']);
    $sth->execute();
    $desc = $sth->fetchAll();

    if($desc) {
      return $this->renderer->render($response, 'detail.phtml', ['desc' => $desc[0]]);
    } else {
      return $this->renderer->render($response, 'index.phtml', $args);
    }

  } else {
    return $this->renderer->render($response, 'home.phtml', $args);
  }
});

$app->post('/register', function (Request $request, Response $response) {

  $input = $request->getParsedBody();
  $sql = "INSERT INTO users (role_id, username, email, password, f_name, l_name, b_date, created_at, phone) VALUES (1, :username, :email, :password, :firstname, :lastname, :dateofbirth, :created_at, :phone)";
  $sth = $this->db->prepare($sql);

  $sth->bindParam("username", filter_var($input['username'], FILTER_SANITIZE_STRING));
  $sth->bindParam("email", filter_var($input['email'], FILTER_SANITIZE_EMAIL));
  $sth->bindParam("password", password_hash($input['password'], PASSWORD_DEFAULT));
  $sth->bindParam("firstname", $input['firstname']);
  $sth->bindParam("lastname", $input['lastname']);
  $sth->bindParam("dateofbirth", $input['dateofbirth']);
  $sth->bindParam("created_at", date('Y-m-d H:i:s'));
  $sth->bindParam("phone", json_encode($phones = array()));

  $this->validator->request($request, [
    'username' => [
      'rules' => V::length(3, 25)->alnum('_')->noWhitespace(),
      'messages' => [
        'noWhitespace' => 'Username shouldn\'t contain any white spaces.',
        'alnum' => 'Username must contain only letters (a-z), digits (0-9) and "_".',
        'length' => 'Username should be 3 to 25 characters long.'
      ]
    ],
    'password' => [
      'rules' => V::noWhitespace()->length(6, 25),
      'messages' => [
        'length' => 'The password length must be between {{minValue}} and {{maxValue}} characters.',
        'noWhitespace' => 'The password shouldn\'t contain any white spaces.'
      ]
    ],
    'email' => [
      'rules' => V::email(),
      'messages' => [
        'email' => 'The email entered is not of a correct email format.'
      ]
    ],
    'firstname' => [
      'rules' => V::length(1, 25)->alpha()->noWhitespace(),
      'messages' => [
        'noWhitespace' => 'First name shouldn\'t contain any white spaces.',
        'alpha' => 'First name needs to contains alpha characters only.',
        'length' => 'First name should be 1 to 25 characters long.'
      ]
    ],
    'lastname'=> [
      'rules' => V::length(1, 25)->alpha(),
      'messages' => [
        'alpha' => 'Last name needs to contains alpha characters only.',
        'length' => 'Last name should be 1 to 25 characters long.'
      ]
    ],
  ]);

  // if email or username already registered
  $sql2 = "SELECT * FROM users WHERE username=:username OR email=:email";
  $sth2 = $this->db->prepare($sql2);

  $sth2->bindParam("username", filter_var($input['username'], FILTER_SANITIZE_STRING));
  $sth2->bindParam("email", filter_var($input['email'], FILTER_SANITIZE_EMAIL));

  $sth2->execute();

  if($sth2->fetchAll()) {
    $this->validator->addError('username', 'This username/email is already used.');
  }

  if ($this->validator->isValid()) {
    $sth->execute();
    $lastInsertId=$this->db->lastInsertId();
    //$this->flash('success', 'Your account has been created.');
    if ($lastInsertId) {
      $session = $this->session;
      $session->set('uid', $lastInsertId);
      $session->set('role', 1);
      $session->set('username', filter_var($input['username'], FILTER_SANITIZE_STRING));
    }

    return $response->withRedirect("/dashboard");

  } else {
    $errors = $this->validator->getErrors();
    return $this->renderer->render($response, 'index.phtml',  ['errors' => $errors]);
  }

});
