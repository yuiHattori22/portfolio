<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
</head>

<body>
    <form method="post">
        <div class="login">
            <p>ログイン</p>
            <p>ユーザーid:<input type="text" name="login_user_id"></p>
            <p>パスワード:<input type="password" name="login_password" autocomplete="new-password"></p>
            <button type="submit" name="login">ログイン</button>
        </div>
        <p>-------------------------------</p>
        <div class="reg">
            <p>会員登録</p>
            <p>ユーザー名:<input type="text" name="user_name"></p>
            <p>メールアドレス:<input type="text" name="mailadress"></p>
            <p>ユーザーid:<input type="text" name="user_id"></p>
            <p>パスワード:<input type="password" name="password"></p>
            <p>再パスワード:<input type="password" name="check_pass"></p>
            <button type="submit" name="user_create">新規会員登録</button>
        </div>
    </form>

    <?php
    session_start();
    require 'db_connect.php';
    $dbh = getConnection();

    try {
        // ログイン処理の場合
        if (isset($_POST['login'])) {
            // ログインに必要な情報が入力されていない場合
            if (!isset($_POST['login_user_id']) || !isset($_POST['login_password'])) {
                echo 'ログイン情報が正常に入力されていません。';
            } else {
                // パスワードをハッシュ値に変換
                $password = hash('sha256', $_POST['login_password']);

                // user_infoテーブルからユーザー情報を取得
                $query = "SELECT * 
                FROM user_info
                WHERE user_id=:user_id AND password=:password";

                $stmt = $dbh->prepare($query);
                $stmt->bindValue(':user_id', $_POST['login_user_id'], PDO::PARAM_STR);
                $stmt->bindValue(':password', $password, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // ユーザー情報が取得できた場合
                if ($user) {
                    // ユーザー情報をセッションに保存
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_name'] = $user['user_name'];
                    $_SESSION['user_icon'] = $user['user_icon'];

                    // timeline.phpに遷移
                    header('Location: timeline.php');
                    exit();
                }
                // 取得できなかった場合 
                else {
                    echo 'ユーザーidまたはパスワードが間違っています。';
                }
            }
        }
        // 会員登録処理の場合
        elseif (isset($_POST['user_create'])) {
            // 会員登録に必要な情報が入力されていない場合
            if (!isset($_POST['mailadress']) || !isset($_POST['user_id']) || !isset($_POST['password'])) {
                echo '会員登録に必要な情報が足りていません。';
            }
            // 再確認用パスワードが合致しない場合
            if ($_POST['password'] != $_POST['check_pass']) {
                echo 'パスワードが異なっています。';
            } else {
                // user_infoテーブルに同一のuser_idが登録されていないか確認
                $query = "SELECT * 
                FROM user_info
                WHERE user_id=:user_id";

                $stmt = $dbh->prepare($query);
                $stmt->bindValue(':user_id', $_POST['user_id'], PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // 既に登録されている場合
                if ($user) {
                    echo 'そのユーザーidは既に使用されています。';
                } else {
                    // パスワードをハッシュ地に変換
                    $password = hash('sha256', $_POST['password']);
                    // user_infoにユーザー情報を追加
                    $create_user = "INSERT INTO user_info (user_name, mailadress, user_id, password) 
                    VALUES (:user_name, :mailadress, :user_id, :password)";

                    $stmt = $dbh->prepare($create_user);
                    $stmt->bindValue(':user_name', $_POST['user_name'], PDO::PARAM_STR);
                    $stmt->bindValue(':mailadress', $_POST['mailadress'], PDO::PARAM_STR);
                    $stmt->bindValue(':user_id', $_POST['user_id'], PDO::PARAM_STR);
                    $stmt->bindValue(':password', $password, PDO::PARAM_STR);
                    $stmt->execute();

                    // ユーザー情報をセッションに保存
                    $_SESSION['user_id'] = $_POST['user_id'];
                    $_SESSION['user_name'] = $_POST['user_name'];
                    // user_iconにはデフォルトで「default.png」が登録される
                    $_SESSION['user_icon'] = '../images/userIcon/default.png';

                    // timeline.phpに遷移
                    header('Location: timeline.php');
                    exit();
                }
            }
        }
    } catch (Exception $e) {
        echo 'エラーが発生しました。', $e->getMessage();
    }
    ?>
</body>

</html>