<?php
// dbへのセッション接続用関数
function getConnection()
{
    // .envファイルからdbの情報を取得
    require __DIR__ . '/../../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dsn = "mysql:dbname={$_ENV['DB_NAME']};host={$_ENV['DB_HOST']}";

    try {
        // db接続
        $dbh = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbh;
    } catch (PDOException $e) {
        echo 'データベース接続に失敗しました。', $e->getMessage();
        return null;
    }
}
