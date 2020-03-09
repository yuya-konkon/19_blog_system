<?php

// 接続に必要な情報を定数として定義
define('DSN', 'mysql:host=db;dbname=blog_system;charset=utf8');
define('USER', 'blog_admin');
define('PASSWORD', '9999');

// Noticeというエラーを非表示にする
error_reporting(E_ALL & ~E_NOTICE);
