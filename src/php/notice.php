<?php
session_start();
// db接続
require 'db_connect.php';
$dbh = getConnection();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>通知</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'header_prof.php'; ?>
    <?php
    // eventsテーブルから対象が自分のイベントを全て取得
    $event = "SELECT e.event_id, e.event, e.event_user, e.post_id, e.comment_id, e.event_datetime, e.is_read, p.oshi_name, c.comment_text
    FROM events AS e
    LEFT JOIN posts AS p ON e.post_id = p.post_id
    LEFT JOIN comments AS c ON e.comment_id = c.comment_id
    INNER JOIN user_info AS info ON e.event_receiver = info.user_id
    WHERE e.event_receiver = :event_receiver 
    ORDER BY e.event_datetime DESC";

    $stmt = $dbh->prepare($event);
    $stmt->bindValue(':event_receiver', $_SESSION['user_id'], PDO::PARAM_STR);
    $stmt->execute();

    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($notices as $notice) {
        // user_infoテーブルからイベントを行ったユーザーを取得
        $get_user = "SELECT *
        FROM user_info 
        WHERE user_id = :user_id";

        $stmt = $dbh->prepare($get_user);
        $stmt->bindValue(':user_id', $notice['event_user'], PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $text = '';

        // イベントによって表示する文字列を変更
        // フォローイベントの場合
        if ($notice['event'] === 'follow') {
            $text = "{$user['user_name']}さんからフォローされました。";
            $page = "prof.php?user_id=" . htmlspecialchars($notice['user_id']) . "";
        }
        // いいねイベントの場合
        elseif ($notice['event'] === 'heart') {
            // 投稿に対するイベントの場合
            if (isset($notice['post_id'])) {
                $text = "{$user['user_name']}さんが「{$notice['oshi_name']}」の布教投稿をいいねしました。";
                $page = "detail.php?user_id=" . htmlspecialchars($notice['post_id']) . "";
            }
            // コメントに対するイベントの場合
            elseif (isset($notice['comment_id'])) {
                $text = "{$user['user_name']}さんが「{$notice['comment_text']}」のコメントをいいねしました。";
                $page = "detail.php?user_id=" . htmlspecialchars($notice['post_id']) . "";
            } else {
                echo '不正なデータです。';
                exit;
            }
        }
        // コメントイベントの場合
        elseif ($notice['event'] === 'comment') {
            // 投稿に対するイベントの場合
            if (isset($notice['post_id'])) {
                $text = "{$user['user_name']}さんが「{$notice['oshi_name']}」の布教投稿にコメントしました。";
            }
            // コメントに対するイベントの場合
            elseif (isset($notice['comment_id'])) {
                $text = "{$user['user_name']}さんが「{$notice['comment_text']}」のコメントにコメントしました。";
            } else {
                echo '不正なデータです。';
                exit;
            }
        } else {
            echo '不正なデータです。';
            exit;
        }

        // 通知を表示
        echo "
        <div class='notice'>
            <!-- 通知クリック時に対象のページへ遷移 -->
            <a href='detail.php?post_id=" . htmlspecialchars($notice['post_id']) . "'>
                <form method='post' action='prof.php'>
                    <button type='submit' name='user_id' value='" . htmlspecialchars($user['user_id']) . "' class='prof_btn'>
                        <img src='" . htmlspecialchars($user['user_icon']) . "' alt='画像' class='timeline_icon'>
                    </button>
                </form>
                <p>$text</p>
            </a>
        </div>
        ";
    }
    ?>
    <?php include 'footer.php'; ?>
</body>

</html>