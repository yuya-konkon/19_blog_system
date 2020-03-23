<?php

require_once('config.php');
require_once('functions.php');

session_start();

$dbh = connectDb();

$sql = <<<SQL
select
  p.*,
  c.name,
  u.name as user_name
from
  posts p
left join
  categories c
on p.category_id = c.id
left join
  users u
on p.user_id = u.id
order by
  created_at desc
SQL;

$stmt = $dbh->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <!-- main -->
    <div class="container">
      <div class="row">
        <div class="col-sm-11 col-md-10 col-lg-9 mx-auto">
          <div class="row">
            <?php foreach ($posts as $post) : ?>
              <div class="col-md-6">
                <div class="article">
                  <h3 class="blog-title"><a href="show.php?id=<?php echo h($post['id']); ?>"><?php echo h($post['title']); ?></a></h3>
                  <p>著者 : <?php echo h($post['user_name']); ?></p>
                  <p>作成日 : <?php echo h($post['crated_at']); ?></p>
                  <p> <?php echo nl2br(h(mb_strimwidth($post['body'], 0, 50, "..."))) ?></p>
                </div>
                <hr>
              </div>
              <?php endforeach; ?>
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