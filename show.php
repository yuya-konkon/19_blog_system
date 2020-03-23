<?php

require_once('config.php');
require_once('functions.php');

session_start();

$id = $_GET['id'];

if (!is_numeric($id)) {
  header('Location: index.php');
  exit;
}

$dbh = connectDB();
// ヒアドキュメント <<<でSQLと同じ文字出てくるまで1つなぎとする
$sql = <<<SQL
select
  p.*,
  c.name
from
  posts p
left join
  categories c
on
  p.category_id = c.id
where
  p.id = :id
SQL;

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (empty($post)) {
  header('Location: index.php');
  exit;
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
      <div class="collapse navbar-collapse" id="navbarToggle">
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
    <!-- 内容 -->
    <div class="container">
      <div class="row">
        <div class="com-md-11 col-lg-9 mx-auto mt-5">
          <h2><?php echo h($post['title']); ?></h2>
          <p>投稿日 : <?php echo h($post['created_at']); ?></p>
          <p>カテゴリー : <?php echo h($post['name']); ?></p>
          <hr>
          <p>
            <?php echo nl2br(h($post['body'])); ?>
          </p>
          <!-- edit.phpにidをパラメーターとして渡す -->
          <?php if (($_SESSION['id']) && ($_SESSION['id'] == $post['user_id'])) : ?>
            <a href="edit.php?id=<?php echo h($post['id']); ?>" class="btn btn-secondary">編集</a>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#post-delete">削除</button>
          <?php endif; ?>
          <!-- index.phpに戻る -->
          <a href="index.php" class="btn btn-info">戻る</a>
        </div>
      </div>
    </div>
    <!-- footer -->
    <footer class="footer font-small bg-dark">
      <div class="footer-copyright text-center py-3 text-light">&copy; 2020 Camp Blog</div>
    </footer>
  </div>
  <div class="modal fade" id="post-delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">
            「<?php echo h($post['title']); ?>」の記事を削除しますか？
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p><?php echo nl2br(h($post['body'])); ?></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
          <a href="delete.php?id=<?php echo h($post['id']); ?>" class="btn btn-warning">削除</a>
        </div>
      </div>
    </div>
  </div>
</body>
</body>

</html>