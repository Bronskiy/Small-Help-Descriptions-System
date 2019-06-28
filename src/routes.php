<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

//Checking if user logged in
$isAuthorized = function ($request, $response, $next) {
  $session = $this->session;
  if (isset($session->uid)) {
    $response = $next($request, $response);
  } else {
    return $response->withRedirect("/");
  }
  return $response;
};

//Checking if user has access to Assignment 3 and Assignment 4
$hasFullAccess = function ($request, $response, $next) {
  $session = $this->session;
  if ($session->role == 2) {
    $response = $next($request, $response);
  } else {
    return $response->withRedirect("/");
  }
  return $response;
};


// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
  return $this->renderer->render($response, 'home.phtml', $args);
});

$app->get('/404', function (Request $request, Response $response, array $args) {
  return $this->renderer->render($response, '404.phtml', $args);
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
    if ($input['new_desc_id'] != '') {
      return $this->renderer->render($response, 'add-desc.phtml', ['new_desc_id' => $input['new_desc_id']]);
    } else {
      return $response->withRedirect("/descriptions");
    }
  } else {
    // TODO: Wrong password message
    return $response->withRedirect("/");
  }
});

$app->get('/sign-out', function (Request $request, Response $response) {
  $session = $this->session;
  $session::destroy();
  return $response->withRedirect("/");
});

$app->post('/search', function (Request $request, Response $response) {
  $input = $request->getParsedBody();
  $session = $this->session;
  if ($session->role != 2) {
    $sql = "SELECT t1.id, t1.subject, t1.hidden, t1.description_body, t1.user_id, t2.username
    FROM description t1
    LEFT JOIN users t2
    ON t1.user_id = t2.id
    WHERE (t1.subject LIKE :query OR t1.description_body LIKE :query) AND isnull(t1.hidden)";
  } else {
    $sql = "SELECT t1.id, t1.subject, t1.hidden, t1.description_body, t1.user_id, t2.username
    FROM description t1
    LEFT JOIN users t2
    ON t1.user_id = t2.id
    WHERE t1.subject LIKE :query OR t1.description_body LIKE :query";
  }
  $sth = $this->db->prepare($sql);
  $query = "%".$input['keyword']."%";
  $sth->bindParam("query", $query);
  $sth->execute();
  $descriptions = $sth->fetchAll();
  if ($descriptions) {
    return $this->renderer->render($response, 'results.phtml', ['descriptions' => $descriptions]);
  } else {
    return $this->renderer->render($response, 'results.phtml', ['descriptions' => []]);
  }
});

$app->group('/users', function () use ($app) {

  $app->get('', function (Request $request, Response $response, array $args) {
    $sql = "SELECT id, role_id, username, email, created_at FROM users";
    $sth = $this->db->prepare($sql);
    $sth->execute();
    $users = $sth->fetchAll();
    if($users) {
      return $this->renderer->render($response, 'users.phtml', ['users' => $users]);
    }
  });

  $app->get('/add', function (Request $request, Response $response, array $args) {
    return $this->renderer->render($response, 'add-user.phtml', $args);
  });

  $app->post('/update', function (Request $request, Response $response, array $args) {
    $input = $request->getParsedBody();
    if ($input['id'] != '') {
      if ($input['userPassword'] != '') {
        $sql = "UPDATE users
        SET username = :username,
        email = :userEmail,
        role_id = :userRole,
        password = :userPassword
        WHERE id = :id";
      } else {
        $sql = "UPDATE users
        SET username = :username,
        email = :userEmail,
        role_id = :userRole
        WHERE id = :id";
      }

      $sth = $this->db->prepare($sql);
      if ($input['userPassword'] != '') {
        $sth->bindParam("userPassword", password_hash($input['userPassword'], PASSWORD_DEFAULT));
      }
      $sth->bindParam("id", $input['id']);
      $sth->bindParam("username", filter_var($input['username'], FILTER_SANITIZE_STRING));
      $sth->bindParam("userEmail", filter_var($input['userEmail'], FILTER_SANITIZE_EMAIL));
      $sth->bindParam("userRole", $input['userRole']);
      $sth->execute();

      return $response->withRedirect("/users");

    } else {
      return $response->withRedirect("/404");
    }
  });

  $app->get('/{id}/edit', function (Request $request, Response $response, array $args) {
    if ($args['id'] != '') {
      $sql = "SELECT id, role_id, username, email FROM users WHERE id = :id";
      $sth = $this->db->prepare($sql);
      $sth->bindParam("id", $args['id']);
      $sth->execute();
      $user = $sth->fetchAll();
      return $this->renderer->render($response, 'edit-user.phtml', ['user' => $user[0]]);
    } else {
      return $response->withRedirect("/404");
    }
  });

  $app->get('/{id}/delete', function (Request $request, Response $response, array $args) {
    // TODO: don't remove yourself. expm1: $session->uid != $args['id']
    if ($args['id'] != '') {
      $sql = "DELETE FROM users WHERE id = :id";
      $sth = $this->db->prepare($sql);
      $sth->bindParam("id", $args['id']);
      $sth->execute();
      // TODO: Confirmation
      return $response->withRedirect("/users");
    } else {
      return $response->withRedirect("/404");
    }
  });

})->add($isAuthorized)->add($hasFullAccess);

$app->get('/phonebook/get', function (Request $request, Response $response, array $args) {
  $session = $this->session;
  if ($session->role != 2) {
    $sql = "SELECT id, subject, hidden, 'notAdmin' as user,
    CASE
           WHEN hidden = 'on' THEN 'Hidden'
           WHEN isnull(hidden) THEN ''
           ELSE hidden
         END AS hidden
    FROM description
    WHERE isnull(hidden)
    ORDER BY subject;";
  } else {
    $sql = "SELECT id, subject, hidden, 'admin' as user,
    CASE
           WHEN hidden = 'on' THEN 'Hidden'
           WHEN isnull(hidden) THEN ''
           ELSE hidden
         END AS hidden
    FROM description
    ORDER BY subject;";
  }

  $sth = $this->db->prepare($sql);

  $sth->execute();
  $descriptions = $sth->fetchAll();


  if($descriptions) {
      return $response->withJson($descriptions);
    } else {
      return $response->withJson([]);
    }

});

$app->group('/descriptions', function () use ($app, $isAuthorized) {

  $app->get('', function (Request $request, Response $response, array $args) {
    $session = $this->session;
    if ($session->role != 2) {
      $sql = "SELECT t1.id, t1.subject, t1.hidden, t1.description_body, t1.created_at, t1.user_id, t2.username
      FROM description t1
      LEFT JOIN users t2
      ON t1.user_id = t2.id
      WHERE isnull(t1.hidden)
      ORDER BY t1.subject;";
    } else {
      $sql = "SELECT t1.id, t1.subject, t1.hidden, t1.description_body, t1.created_at, t1.user_id, t2.username
      FROM description t1
      LEFT JOIN users t2
      ON t1.user_id = t2.id
      ORDER BY t1.subject;";
    }
    $sth = $this->db->prepare($sql);
    $sth->execute();
    $descriptions = $sth->fetchAll();
    if($descriptions) {
      return $this->renderer->render($response, 'descriptions.phtml', ['descriptions' => $descriptions]);
    }
  });

  $app->post('/add', function (Request $request, Response $response, array $args) {
    $input = $request->getParsedBody();
    if ($input['descId'] != '') {
      $sql = "INSERT INTO description (id, subject, hidden, description_body, created_at, user_id) VALUES (:id, :subject, :hidden, :description_body, :created_at, :user_id)";
    } else {
      $sql = "INSERT INTO description (subject, hidden, description_body, created_at, user_id) VALUES (:subject, :hidden, :description_body, :created_at, :user_id)";
    }
    $sth = $this->db->prepare($sql);
    $session = $this->session;
    if ($input['descId'] != '') {
      $sth->bindParam("id", $input['descId']);
    }
    $sth->bindParam("subject", $input['subject']);
    $sth->bindParam("hidden", $input['hiddenDesc']);
    $sth->bindParam("description_body", $input['description_body']);
    $sth->bindParam("created_at", date('Y-m-d H:i:s'));
    $sth->bindParam("user_id", $session->uid);
    $sth->execute();
    return $response->withRedirect("/descriptions");

  })->add($isAuthorized);

  $app->get('/add', function (Request $request, Response $response, array $args) {
    return $this->renderer->render($response, 'add-desc.phtml', $args);
  })->setName('add-desc')->add($isAuthorized);

  $app->post('/update', function (Request $request, Response $response, array $args) {
    $input = $request->getParsedBody();
    if ($input['id'] != '') {

      if ($input['id'] != $input['newId']) {
        $sql = "UPDATE description
        SET subject = :subject,
        hidden = :hidden,
        description_body = :description_body,
        id = :new_id
        WHERE id = :id";
      } else {
        $sql = "UPDATE description
        SET subject = :subject,
        hidden = :hidden,
        description_body = :description_body
        WHERE id = :id";
      }

      $sth = $this->db->prepare($sql);

      if ($input['id'] != $input['newId']) {
        $sth->bindParam("new_id", $input['newId']);
      }

      $sth->bindParam("id", $input['id']);
      $sth->bindParam("hidden", $input['hiddenDesc']);
      $sth->bindParam("subject", $input['subject']);
      $sth->bindParam("description_body", $input['description_body']);
      $sth->execute();
      if ($input['id'] != $input['newId']) {
        $newRoute = "/descriptions/" . $input['newId'] . "/edit";
      } else {
        $newRoute = "/descriptions/" . $input['id'] . "/edit";
      }
      return $response->withRedirect($newRoute);

    } else {
      return $response->withRedirect("/404");
    }
  })->add($isAuthorized);

  $app->get('/{id}/edit', function (Request $request, Response $response, array $args) {
    if ($args['id'] != '') {
      $sql = "SELECT * FROM description WHERE id = :id";
      $sth = $this->db->prepare($sql);

      $sth->bindParam("id", $args['id']);
      $sth->execute();
      $description = $sth->fetchAll();
      if($description) {
        //var_dump($description);
        return $this->renderer->render($response, 'edit-desc.phtml', ['description' => $description[0]]);
      } else {
        return $response->withRedirect("/404");
      }
    } else {
      return $response->withRedirect("/404");
    }
  })->add($isAuthorized);

  $app->get('/{id}/delete', function (Request $request, Response $response, array $args) {
    // TODO: don't remove yourself. expm1: $session->uid != $args['id']
    if ($args['id'] != '') {
      $sql = "DELETE FROM description WHERE id = :id";
      $sth = $this->db->prepare($sql);
      $sth->bindParam("id", $args['id']);
      $sth->execute();
      // TODO: Confirmation
      return $response->withRedirect("/descriptions");
    } else {
      return $response->withRedirect("/404");
    }
  })->add($isAuthorized);

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
if ($args['id'] != '' && is_numeric($args['id'])) {
  //  $sql = "SELECT * FROM description WHERE id=:id";
  $sql = "SELECT t1.id, t1.subject, t1.hidden, t1.description_body, t1.created_at, t1.user_id, t2.username
  FROM description t1
  LEFT JOIN users t2
  ON t1.user_id = t2.id
  WHERE t1.id=:id
  ORDER BY t1.subject;";
  $sth = $this->db->prepare($sql);

  $sth->bindParam("id", $args['id']);
  $sth->execute();
  $desc = $sth->fetchAll();

  if($desc) {
    return $this->renderer->render($response, 'detail.phtml', ['desc' => $desc[0]]);
  } else {
    return $this->renderer->render($response, 'index.phtml', ['new_desc_id' => $args['id']]);
    //return $response->withRedirect($this->router->pathFor('add-desc', ['new_desc_id' => $args['id']], []));
    //return $response->withRedirect("/404");

  }

} else {
  return $this->renderer->render($response, 'home.phtml', $args);
}
});

$app->post('/register', function (Request $request, Response $response) {

  $input = $request->getParsedBody();
  $sql = "INSERT INTO users (role_id, username, email, password, created_at) VALUES (:role_id, :username, :email, :password, :created_at)";
  $sth = $this->db->prepare($sql);

  $sth->bindParam("role_id", $input['userRole']);
  $sth->bindParam("username", filter_var($input['username'], FILTER_SANITIZE_STRING));
  $sth->bindParam("email", filter_var($input['userEmail'], FILTER_SANITIZE_EMAIL));
  $sth->bindParam("password", password_hash($input['userPassword'], PASSWORD_DEFAULT));
  $sth->bindParam("created_at", date('Y-m-d H:i:s'));

  $this->validator->request($request, [
    'username' => [
      'rules' => V::length(3, 25)->alnum('_')->noWhitespace(),
      'messages' => [
        'noWhitespace' => 'Username shouldn\'t contain any white spaces.',
        'alnum' => 'Username must contain only letters (a-z), digits (0-9) and "_".',
        'length' => 'Username should be 3 to 25 characters long.'
      ]
    ],
    'userPassword' => [
      'rules' => V::noWhitespace()->length(6, 25),
      'messages' => [
        'length' => 'The password length must be between {{minValue}} and {{maxValue}} characters.',
        'noWhitespace' => 'The password shouldn\'t contain any white spaces.'
      ]
    ],
    'userEmail' => [
      'rules' => V::email(),
      'messages' => [
        'email' => 'The email entered is not of a correct email format.'
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
    //$lastInsertId=$this->db->lastInsertId();
    //$this->flash('success', 'Your account has been created.');
    /*
    if ($lastInsertId) {
    $session = $this->session;
    $session->set('uid', $lastInsertId);
    $session->set('role', 1);
    $session->set('username', filter_var($input['username'], FILTER_SANITIZE_STRING));
  }
  */
  return $response->withRedirect("/users");

} else {
  $errors = $this->validator->getErrors();
  return $response->withRedirect("/users");
  //return $this->renderer->render($response, 'users.phtml',  ['errors' => $errors]);
  //return $this->renderer->render($response, 'index.phtml',  ['errors' => $errors]);
}

});
