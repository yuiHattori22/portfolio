<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php
    session_start();
    // db接続
    require 'db_connect.php';
    $dbh = getConnection();

    try {
        // アイコンもしくは名前クリックで遷移してきた場合
        if (isset($_POST['user_id'])) {
            // リダイレクト後もuser_idを保持できるようセッションに保存
            $_SESSION['target_user_id'] = $_POST['user_id'];

            // postによる再送信が行われないようリダイレクト処理
            header('Location: prof.php');
            exit();
        }

        if (isset($_SESSION['target_user_id'])) {
            $user_id = $_SESSION['target_user_id'];
        } else {
            echo "ユーザーIDが取得できませんでした。";
            exit();
        }

        // プロフィール編集後の場合
        if (isset($_POST['edit'])) {
            // ユーザーアイコンが変更されている場合
            if (isset($_POST['user_icon']) && $_POST['user_icon'] != $_SESSION['target_user_icon']) {
                // ユーザーアイコンを格納するディレクトリパス
                $icon_image_dir = '../images/userIcon/';
                // ディレクトリがなければ作成
                if (!is_dir($icon_image_dir)) {
                    mkdir($icon_image_dir, 0777, true);
                }

                $imagePaths = [];
                $user_icon = $_POST['user_icon'];

                // Base64からヘッダー部分を取り除く
                $user_icon = str_replace('data:image/png;base64,', '', $user_icon);
                $user_icon = str_replace(' ', '+', $user_icon);
                // バイナリデータにデコード
                $user_icon = base64_decode($user_icon);
                // 画像名をユニークな値に設定
                $userIconFileName = 'user_icon_' . uniqid() . '.png';
                // 保存するファイルパスを指定
                $userIconPath = $icon_image_dir . $userIconFileName;
                // 画像を保存
                file_put_contents($userIconPath, $user_icon);

                // 元のアイコンがデフォルトのものでなく、かつディレクトリに元のアイコンファイルがあれば
                if ($_SESSION['target_user_icon'] != '../images/userIcon/default.png'  && file_exists($_SESSION['target_user_icon'])) {
                    // ファイルを削除
                    unlink($_SESSION['target_user_icon']);
                }
            }

            // ユーザーアイコンが変更されている場合は変更後のファイル名を格納
            $edit_user_icon = isset($userIconPath) ? $userIconPath : '';
            $edit_user_name = $_POST['user_name'];
            $edit_user_profile = $_POST['user_profile'];

            $params = [
                ':user_name' => $edit_user_name,
                ':user_profile' => $edit_user_profile,
                ':user_id' => $user_id,
            ];

            // user_infoテーブルを更新
            $edit_profile = "UPDATE user_info
            SET user_name = :user_name, user_profile = :user_profile";

            // ユーザーアイコンが変更されている場合
            if ($edit_user_icon != '') {
                // user_iconカラムの値を更新するsqlを追加
                $edit_profile .= ", user_icon = :user_icon";
                $params[':user_icon'] = $edit_user_icon;
            }

            $edit_profile .= " WHERE user_id = :user_id";

            $stmt = $dbh->prepare($edit_profile);
            $stmt->execute($params);

            // 変更したuser_iconをセッションに保存
            $_SESSION['user_icon'] = $edit_user_icon;

            // postの再送信を防ぐためリダイレクト処理
            header('Location: prof.php');
            exit();
        }

        // 対象ユーザーの投稿を取得(なければユーザー情報のみ取得)
        $query = "SELECT * 
            FROM user_info as info 
            LEFT JOIN posts as p 
            ON info.user_id = p.user_id 
            WHERE info.user_id = :user_id 
            ORDER BY COALESCE(p.create_datetime, '1970-01-01') DESC";

        $stmt = $dbh->prepare($query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ユーザー情報が取得できた場合
        if (!empty($posts)) {
            // $_SESSION['target_user_icon']を取得した値に更新
            // アイコンは編集時に前の画像データを削除する必要があるためセッションに保存
            $_SESSION['target_user_icon'] = $posts[0]['user_icon'];
            // 名前とプロフはuser_idから取得できるため通常の変数に保存
            $user_name = $posts[0]['user_name'];
            $user_profile = $posts[0]['user_profile'];
        } else {
            echo "ユーザー情報を正常に取得できませんでした。";
        }
    } catch (Exception $e) {
        echo 'エラーが発生しました。', $e->getMessage();
    }

    // 対象ユーザーが自分の場合
    if ($user_id === $_SESSION['user_id']) {
        echo "
        <header>
            <!-- prof_edit.phpに戻ってしまう可能性があるため、timeline.phpに強制的に戻す -->
            <a href='timeline.php'>
                <input type='image' src='../images/buttonIcon/return.png' alt='戻る' class='return'>
            </a>
            <form method='post' class='logout_form' action='timeline.php'>
                <button type='submit' name='logout' class='logout_btn'>ログアウト</button>
            </form>
        </header>";
    }
    // それ以外は通常通りのヘッダー
    else {
        include 'header_return.php';
    }
    ?>

    <div class="profile">
        <!-- ユーザーアイコンを画像表示モーダルで拡大表示 -->
        <img src="<?php echo $_SESSION['target_user_icon']; ?>" alt="画像" class="prof_icon" onclick='showModal(this.src, event)'>
        <p class="user_name"><?php echo $user_name; ?></p>
        <p class="user_profile"><?php echo $user_profile; ?></p>

        <?php

        // 対象ユーザーが自分の場合
        if ($user_id === $_SESSION['user_id']) {
            // 編集ボタンを表示
            echo "
            <form method='post' action='prof_edit.php'>
                <input type='hidden' name='user_icon' value='" . htmlspecialchars($_SESSION['target_user_icon']) . "'>
                <input type='hidden' name='user_name' value='" . htmlspecialchars($user_name) . "'>
                <input type='hidden' name='user_profile' value='" . htmlspecialchars($user_profile) . "'>
                <button class='edit' name='edit'>編集</button>
            </form>";
        }
        // それ以外の場合
        else {
            // フォローボタンを表示
            echo "<button data-following='false' 
                  data-followerId='" . htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') . "' 
                  data-followId='" . htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8') . "' 
                  id='follow' class='follow'>フォローする</button>";
        }
        ?>
    </div>
    <div class="timeline">
        <?php
        // もし対象ユーザーが投稿していた場合、投稿を表示
        if (isset($posts[0]['post_id'])) {
            include 'list.php';
        }
        ?>
    </div>

    <form action="post.php" method="post" class="post_form">
        <button type="submit" name="post_btn" id="post_btn" class="post_btn">布教する！</button>
    </form>

    <?php include 'modal.php'; ?>
    <?php include 'footer.php'; ?>

    <script src="../js/timeline.js"></script>
    <script src="../js/follow.js"></script>
</body>

</html>