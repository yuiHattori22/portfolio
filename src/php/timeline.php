<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>推し活SNS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'header_prof.php'; ?>
    <?php
    try {
        // db接続
        require 'db_connect.php';
        $dbh = getConnection();

        // 投稿ボタンが押された場合
        if (isset($_POST['post_btn'])) {
            // メイン画像が入っていた場合
            if (isset($_POST['main_image'])) {
                // 画像格納用フォルダのパスを格納
                $main_image_dir = '../images/mainImage/';
                $group_image_dir = '../images/groupImage/';

                // フォルダがなければ作成
                if (!is_dir($main_image_dir)) {
                    mkdir($main_image_dir, 0777, true);
                }
                if (!is_dir($group_image_dir)) {
                    mkdir($group_image_dir, 0777, true);
                }

                $imagePaths = [];
                $main_image = $_POST['main_image'];
                $group_image = $_POST['group_image'];

                // Base64からヘッダー部分を取り除く
                $main_image = str_replace('data:image/png;base64,', '', $main_image);
                $main_image = str_replace(' ', '+', $main_image);

                $group_image = str_replace('data:image/png;base64,', '', $group_image);
                $group_image = str_replace(' ', '+', $group_image);

                // バイナリデータにデコード
                $main_image = base64_decode($main_image);
                $group_image = base64_decode($group_image);

                $mainImageFileName = 'main_image_' . uniqid() . '.png';
                $groupImageFileName = 'group_image_' . uniqid() . '.png';

                // 保存するファイルパスを指定
                $mainImagePath = $main_image_dir . $mainImageFileName;
                $groupImagePath = $group_image_dir . $groupImageFileName;

                // 画像を保存
                file_put_contents($mainImagePath, $main_image);
                file_put_contents($groupImagePath, $group_image);
            }

            // postsテーブルに投稿の情報を作成
            $insertPost = "INSERT INTO posts (user_id, oshi_name, oshi_sex, dimension, genre, group_name, appeal_point, 
                appeal_movie1, appeal_movie2, appeal_movie3, appeal_movie4, main_image, group_image) 
                VALUES (:user_id, :oshi_name, :oshi_sex, :dimension, :genre, :group_name, :appeal_point, 
                :appeal_movie1, :appeal_movie2, :appeal_movie3, :appeal_movie4, :main_image, :group_image)";
            $stmt = $dbh->prepare($insertPost);

            $params = [
                ':user_id' => $_SESSION['user_id'],
                ':oshi_name' => $_POST['oshi_name'],
                ':oshi_sex' => $_POST['oshi_sex'],
                ':dimension' => $_POST['dimension'],
                ':genre' => $_POST['genre'],
                ':group_name' => $_POST['group_name'] ?? null,
                ':appeal_point' => $_POST['appeal_point'],
                ':appeal_movie1' => $_POST['appeal_movie1'] ?? null,
                ':appeal_movie2' => $_POST['appeal_movie2'] ?? null,
                ':appeal_movie3' => $_POST['appeal_movie3'] ?? null,
                ':appeal_movie4' => $_POST['appeal_movie4'] ?? null,
                ':main_image' => $mainImagePath ?? null,
                ':group_image' => $groupImagePath ?? null,
            ];

            $stmt->execute($params);

            // POST処理後にリダイレクト
            header('Location: timeline.php');
            exit();
        }
        // フォローしているユーザーと自身の投稿を取得し表示
        $query = "SELECT * 
                FROM user_info as info 
                INNER JOIN posts as p 
                ON info.user_id = p.user_id
                LEFT JOIN follows as f
                ON p.user_id = f.follower_id 
                WHERE f.follow_id = :follow_id OR info.user_id = :user_id
                ORDER BY p.create_datetime DESC";

        $stmt = $dbh->prepare($query);
        $stmt->bindValue(':follow_id', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include 'list.php';
        include 'modal.php';
    } catch (Exception $e) {
        echo 'エラーが発生しました。', $e->getMessage(), $e->getCode();
    }
    ?>
    <?php include 'footer.php'; ?>

    <script src="../js/timeline.js"></script>
</body>

</html>