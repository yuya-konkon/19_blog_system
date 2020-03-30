<?php

require_once('config.php');

function getAllcategories()
{
  $dbh = connectDb();

  $sql = "select id, name from categories order by id";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
