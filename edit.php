<?php

require_once('config.php');
require_once('functions.php');

session_start();
$id = $_GET['id'];

if (!is_numeric($id)) {
  header('Location: index.php');
  exit;
}

$dbh = connectDb();

$sql = "select * from posts where id = :id";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($post)) {
  header('Location: index.php');
  exit;
}

$sql = "select * from categories";
$stmt = $dbh->prepare($sql);
$stmt->execute();

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $category_id = $_POST['category_id'];
  $body  = $_POST['body'];

  $errors = [];

  if ($title == '') {
    $errors = 'タイトルが未入力です。';
  }

  if ($category_id == '') {
    $errors = 'カテゴリーが未選択です。';
  }

  if ($body == '') {
    $errors = '本文が未入力です。';
  }

  if (empty($errors)) {
    $sql = <<<SQL
    update
      posts
    set
      title = :title,
      body = :body,
      category_id = category_id
    where
      id = :id
    SQL;

    $stmt = $dbh->prepare($sql);

    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':body', $body, PDO::PARAM_STR);
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: show.php?id={$id}");
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
  <!-- header -->
  <div class="flex-col-area">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5">
      <a href="http://localhost/19_blog_system/index.php" class="navbar-brand">Camp Blog</a>
      <div class="collapse navbar-collapse" id="navbarToggler">
        <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
          <?php if ($_SESSION['id']) : ?>
            <li class="nav-item">
              <a href="sign_out.php" class="nav-link">ログアウト</a>
            </li>
            <li class="nav-item">
              <a href="new.php" class="nav-link">New Post</a>
            </li>
          <?php else : ?>
            <li class="nav-item">
              <a href="sign_in.php" class="nav-link">ログイン</a>
            </li>
            <li class="nav-item">
              <a href="sign_up.php" class="nav-link">アカウント登録</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </nav>

    <!-- main -->
    <div class="container">
      <div class="row">
        <div class="col-sm-11 col-md-9 col-lg-7 mx-auto">
          <div class="card my-5 bg-light">
            <div class="card-body">
              <h5 class="card-title text-center">記事編集</h5>
              <form action="edit.php" method="post">
                <div class="form-group">
                  <label for="title">タイトル</label>
                  <input type="text" class="form-control" required value="<?php echo h($post['title']); ?>" autofocus name="title">
                </div>
                <div class="form-gruop">
                  <label for="category_id">カテゴリー</label>
                  <select name="category_id" class="form-control" required>
                    <option value="" disabled>選択してください。</option>
                    <?php foreach ($categories as $c) : ?>
                      <!-- 三項演算子 -->
                      <option value="<?php echo h($c['id']); ?>" <?php echo $post['category_id'] == $c['id'] ? 'selected' : "" ?>>
                        <?php echo h($c['name']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="body">本文</label><textarea name="body" id="" cols="30" rows="10" class="form-control" required><?php echo $post['body']; ?></textarea>
                </div>
                <div class="form-group">
                  <input type="submit" value="更新" class="btn btn-lg btn-primary btn-block ">
                </div>
              </form>
              <a href="show.php?id=<?php echo h($post['id']); ?>" class="btn btn-info btn-block btn-lg">戻る</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- footer -->
    <footer class="footer font-small bg-dark">
      <div class="footer-copyright text-center py-3 text-light">&copy; 2020 Camp Blog</div>
    </footer>
  </div>
</body>

</html>