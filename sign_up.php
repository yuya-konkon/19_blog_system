<?php

require_once('config.php');
require_once('functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $name = $_POST['name'];
  $password = $_POST['password'];

  $errors = [];

  if ($email == '') {
    $errors[] = 'メールアドレスが未入力です';
  }

  if ($name == '') {
    $errors[] = 'ユーザー名が未入力です';
  }

  if ($password == '') {
    $errors[] = 'パスワードが未入力です';
  }

  // アカウント登録済み確認
  $dbh = connectDB();
  $sql = "select * from users where email = :email";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(":email", $email);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $errors[] = 'すでにメールアドレスが登録されています';
  }

  if(empty($errors)) {
    $sql = "insert into users (email, name, password, created_at, updated_at) values (:email, :name, :password, now(), now())";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':name', $name);
    $pw_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    header('Location: sign_in.php');
    exit;
  }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Camp Blog</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <div class="flex-col-area">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
      <a href="http://localhost/19_blog_system/index.php" class="navbar-brand">Camp Blog</a>
    </nav>

    <div class="container">
      <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
          <div class="card card-signin my-5 bg-light">
            <div class="card-body">
              <h5 class="card-title text-center">アカウント登録</h5>
              <?php if ($errors) :?>
                <ul class="alert alert-danger">
                  <?php foreach ($errors as $error) :?>
                    <li><?php echo $error; ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
              <form action="sign_up.php" method="post">
                <div class="form-group">
                  <label for="email">メールアドレス</label>
                  <input type="email" name="email" id="" class="form-control" autofocus required>
                </div>
                <div class="form-group">
                  <label for="name">ユーザー名</label>
                  <input type="name" name="name" id="" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="password">パスワード</label>
                  <input type="password" name="password" id="" class="form-control" required>
                </div>
                <div class="form-group">
                  <input type="submit" value="新規登録" class="btn btn-lg btn-primary btn-block mt-4">
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <footer class="footer font-small bg-dark">
      <div class="footer-copyright text-center py-3 text-light">&copy; 2020 Camp Blog</div>
    </footer>
  </div>
</body>

</html>