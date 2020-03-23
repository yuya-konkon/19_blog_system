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
$sql = "select * from posts";

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (empty($post)) {
  header('Location: index.php');
  exit;
}

$sql_delete = "delete from posts where id = :id";
$stmt = $dbh->prepare($sql_delete);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

header('Location: index.php');
exit;