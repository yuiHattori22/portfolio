<?php
session_start();
// db接続
require 'db_connect.php';
$dbh = getConnection();

// コメントの度にdetail.phpが呼び出されるため、戻るボタンでdetail.php遷移前のページに戻れるように遷移回数をセッションで保存
$post_id = '';
if (!isset($_SESSION['detail_count'])) {
    $_SESSION['detail_count'] = 0;
}
$_SESSION['detail_count']++;

// コメントによって遷移した場合
if (isset($_POST['comment_btn'])) {
    // コメント対象がコメントの場合
    if (isset($_POST['comment'])) {
        // 受け取ったidを$comment_idと$post_idにそれぞれ格納
        $id = explode(',', $_POST['comment']);
        $post_id = $id[0];
        $comment_id = $id[1];

        // commentsテーブルに作成されたコメント情報を追加
        $query = "INSERT INTO comments (comment_user, post_id, parent_comment_id, comment_text)
                VALUES (:comment_user, :post_id, :parent_comment_id, :comment_text)";

        $params = [
            ':comment_user' => $_SESSION['user_id'],
            ':post_id' => $post_id,
            ':parent_comment_id' => $comment_id,
            ':comment_text' => $_POST['comment_input'],
        ];

        $stmt = $dbh->prepare($query);
        $stmt->execute($params);

        // eventsテーブルに保存するためコメント対象のユーザーを取得
        $getUser = "SELECT comment_user FROM comments WHERE parent_comment_id = :parent_comment_id";
        $stmt = $dbh->prepare($getUser);
        $stmt->bindValue(':parent_comment_id', $comment_id, PDO::PARAM_INT);
        $stmt->execute();
        $event_receiver = $stmt->fetch(PDO::FETCH_ASSOC);

        // eventsテーブルにevent情報を追加
        $eventQuery = "INSERT INTO events (event, event_user, event_receiver, post_id, comment_id, is_read) VALUES (:event, :event_user, :event_receiver, :post_id, :comment_id, :is_read)";

        $params = [
            ':event' => 'comment',
            ':event_user' => $_SESSION['user_id'],
            ':event_receiver' => $event_receiver['comment_user'],
            ':post_id' => $post_id,
            ':comment_id' => $comment_id,
            ':is_read' => 0,
        ];

        $stmt = $dbh->prepare($eventQuery);
        $stmt->execute($params);

        // セッションにpost_idを保存
        $_SESSION['detail_post_id'] = $post_id;
    }
    // コメント対象が投稿の場合 
    elseif (isset($_POST['post'])) {
        // commentsテーブルに作成されたコメント情報を追加
        $query = "INSERT INTO comments (comment_user, post_id, comment_text)
                VALUES (:comment_user, :post_id, :comment_text)";

        $params = [
            ':comment_user' => $_SESSION['user_id'],
            ':post_id' => $_POST['post'],
            ':comment_text' => $_POST['comment_input'],
        ];

        $stmt = $dbh->prepare($query);
        $stmt->execute($params);

        // eventsテーブルに保存するためコメント対象のユーザーを取得
        $getUser = "SELECT user_id FROM posts WHERE post_id = :post_id";
        $stmt = $dbh->prepare($getUser);
        $stmt->bindValue(':post_id', $_POST['post'], PDO::PARAM_INT);
        $stmt->execute();
        $event_receiver = $stmt->fetch(PDO::FETCH_ASSOC);

        // eventsテーブルにevent情報を追加
        $eventQuery = "INSERT INTO events (event, event_user, event_receiver, post_id, is_read) VALUES (:event, :event_user, :event_receiver, :post_id, :is_read)";

        $params = [
            ':event' => 'comment',
            ':event_user' => $_SESSION['user_id'],
            ':event_receiver' => $event_receiver['user_id'],
            ':post_id' => $_POST['post'],
            ':is_read' => 0,
        ];

        $stmt = $dbh->prepare($eventQuery);
        $params += [':is_read' => 0];
        $stmt->execute($params);

        // セッションにpost_idを保存
        $_SESSION['detail_post_id'] = $_POST['post'];
    }
    // 戻るボタンでの遷移時にデータの再送信が行われないようリダイレクト処理
    header('Location: detail.php');
    exit();
}

$detailQuery = "SELECT * 
            FROM user_info as info 
            INNER JOIN posts as p 
            ON info.user_id = p.user_id
            WHERE p.post_id = :post_id";

$stmt = $dbh->prepare($detailQuery);

if (isset($_GET['post_id'])) {
    $stmt->bindValue(':post_id', $_GET['post_id'], PDO::PARAM_INT);
} else {
    $stmt->bindValue(':post_id', $_SESSION['detail_post_id'], PDO::PARAM_INT);
}

$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if ($post['group_name']) {
    if ($post['group_image']) {
        $group = "グループ名:{$post['group_name']}<br>
                <img src='{$post['group_image']}' alt='画像' class='detail_group_image' onclick='showModal(this.src, event)'>";
    } else {
        $group = "グループ名:{$post['group_name']}<br>";
    }
} else {
    $group = '';
}

$appeal_movie = '';
for ($i = 1; $i <= 4; $i++) {
    if ($post["appeal_movie{$i}"]) {
        $appeal_movie .= "おすすめ動画等:{$post["appeal_movie{$i}"]}<br>";
    } else {
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <!-- コメントの度にdetail.phpが呼び出されるため、戻るボタンの処理を変更 -->
    <header>
        <input type="image" src="../images/buttonIcon/return.png" alt="戻る" class="return"
            <?php if (isset($_SESSION['detail_count'])) {
                $detail_count = $_SESSION['detail_count'];
                $_SESSION['detail_count'] = 0;
                echo "onclick='Return($detail_count)';";
            } else {
                echo 'onclick="history.back();"';
            } ?>>
        <form method="post" class="logout_form" action="timeline.php">
            <button type="submit" name="logout" class="logout_btn">ログアウト</button>
        </form>
    </header>

    <div class="detail_padding">
        <div class='post_detail'>
            <!-- アイコンもしくは名前クリック時にprof.phpに遷移 -->
            <form method='post' action='prof.php'>
                <div class='detail_prof'>
                    <button type='submit' name='user_id' value='<?php echo $post['user_id']; ?>' class='prof_btn'>
                        <img src='<?php echo $post['user_icon']; ?>' alt='画像' class='detail_icon'>
                    </button>
                    <button type='submit' name='user_id' value='<?php echo $post['user_id']; ?>' class='prof_btn'>
                        <p class='detail_username'><?php echo $post['user_name']; ?></p>
                    </button>
                </div>
            </form>
            <!-- 投稿の詳細情報を表示 -->
            <div class='detail_post'>
                <img src='<?php echo $post['main_image']; ?>' alt='画像' class='detail_main_image' onclick='showModal(this.src, event)'>
                <p>名前:<?php echo $post['oshi_name']; ?><br>
                    ジャンル:<?php echo $post['genre']; ?><br>
                    <?php echo $group; ?>
                    推しポイント:<?php echo $post['appeal_point']; ?><br>
                    <?php echo $appeal_movie; ?></p>
                <p class='detail_datetime'><?php echo $post['create_datetime']; ?></p>
            </div>
            <!-- 投稿への反応ボタン -->
            <div class='post_icon'>
                <!-- data-heartingの値によっていいね状態を変更 -->
                <input type='image' src='../images/buttonIcon/unhearting.png' class='icon heart' data-hearting='false' data-heartUser='<?php echo htmlspecialchars($_SESSION['user_id']); ?>' data-heartPost='<?php echo $post['post_id']; ?>'>
                <!-- いいね数を表示 -->
                <span class='heart_count' data-heartPost='<?php echo $post['post_id']; ?>'>0</span>
                <!-- クリック時にコメントモーダルを表示 -->
                <input type='image' src='../images/buttonIcon/comment.png' class='icon comment' id='comment' onclick='commentModal(<?php echo $post["post_id"]; ?>, "post")'>
            </div>

            <?php
            // 投稿に対するコメント(親コメントのみ)を取得
            $commentQueary = "SELECT * 
            FROM comments AS c
            INNER JOIN user_info AS info
                ON c.comment_user = info.user_id
            WHERE c.post_id = :post_id AND c.parent_comment_id IS NULL 
            ORDER BY comment_datetime DESC";

            $stmt = $dbh->prepare($commentQueary);
            $stmt->bindValue(':post_id', $post['post_id'], PDO::PARAM_INT);
            $stmt->execute();
            $commentsInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 元投稿のユーザー情報を取得
            $postUserGet = "SELECT *
            FROM posts AS p
            INNER JOIN user_info AS info
                ON p.user_id = info.user_id 
            WHERE p.post_id = :post_id";

            $stmt = $dbh->prepare($postUserGet);
            $stmt->bindValue(':post_id', $post['post_id'], PDO::PARAM_STR);
            $stmt->execute();
            $postUser = $stmt->fetch(PDO::FETCH_ASSOC);

            // 親コメントを表示
            foreach ($commentsInfo as $commentInfo) {
                // ひとつながりのコメントをpost_commentクラスで囲む
                echo "<div class='post_comment'>";
                // post_detailクラスと構造はほとんど同じ
                echo "
                <form method='post' action='prof.php'>
                    <div class='comment_prof'>
                        <p>TO:{$postUser['user_name']}</p>
                        <button type='submit' name='user_id' value='" . htmlspecialchars($commentInfo['user_id']) . "' class='prof_btn'>
                            <img src='" . htmlspecialchars($commentInfo['user_icon']) . "' alt='画像' class='comment_icon'>
                        </button>
                        <button type='submit' name='user_id' value='" . htmlspecialchars($commentInfo['user_id']) . "' class='prof_btn'>
                            <p class='comment_username'>" . htmlspecialchars($commentInfo['user_name']) . "</p>
                        </button>
                    </div>
                </form>
                <div class='comment_detail'>
                    <p>" . htmlspecialchars($commentInfo['comment_text']) . "</p>
                    <p class='detail_datetime'>" . $commentInfo['comment_datetime'] . "</p>
                </div>
                <div class='post_icon'>
                    <!-- それぞれdata-heartCommentにcomment_idを保有 -->
                    <input type='image' src='../images/buttonIcon/unhearting.png' class='icon heart' data-hearting='false' data-heartUser='" . htmlspecialchars($_SESSION['user_id']) . "' 
                    data-heartPost='" . $postUser['post_id'] . "' data-heartComment='" . $commentInfo['comment_id'] . "'>
                    <span class='heart_count' data-heartPost='" . $postUser['post_id'] . "'  data-heartComment='" . $commentInfo['comment_id'] . "'>0</span>
                    <!-- commentModal関数にcomment_idを追加で渡す -->
                    <input type='image' src='../images/buttonIcon/comment.png' class='icon comment' id='comment' onclick='commentModal(" . $postUser['post_id'] . ", \"comment\", " . $commentInfo['comment_id'] . ")'>
                </div>
                ";
                echo "</div>";

                // 子コメントを取得
                $parentCommentGet = "SELECT *
                FROM comments AS c
                INNER JOIN user_info AS info
                    ON c.comment_user = info.user_id
                WHERE parent_comment_id = :parent_comment_id";

                $stmt = $dbh->prepare($parentCommentGet);
                $stmt->bindValue(':parent_comment_id', $commentInfo['comment_id'], PDO::PARAM_INT);
                $stmt->execute();
                $parentComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 子コメントがあればcommentParent関数を呼び出す
                if (isset($parentComments)) {
                    foreach ($parentComments as $parentComment) {
                        commentParent($commentInfo, $postUser, $parentComment, $dbh);
                    }
                }
            }

            // 子コメントを表示
            function commentParent($commentInfo, $postUser, $parentComment, $dbh)
            {
                // 親コメントと構造はほとんど同じ
                echo "
                <div class='parent_comment'>
                    <form method='post' action='prof.php'>
                        <div class='comment_prof'>
                            <p>TO:" . htmlspecialchars($commentInfo['user_name']) . "</p>
                            <button type='submit' name='user_id' value='" . htmlspecialchars($parentComment['user_id']) . "' class='prof_btn'>
                                <img src='" . htmlspecialchars($parentComment['user_icon']) . "' alt='画像' class='comment_icon'>
                            </button>
                            <button type='submit' name='user_id' value='" . htmlspecialchars($parentComment['user_id']) . "' class='prof_btn'>
                                <p class='comment_username'>" . htmlspecialchars($parentComment['user_name']) . "</p>
                            </button>
                        </div>
                    </form>
                    <div class='comment_detail'>
                        <p>" . htmlspecialchars($parentComment['comment_text']) . "</p>
                        <p class='detail_datetime'>" . $parentComment['comment_datetime'] . "</p>
                    </div>
                    <div class='post_icon'>
                        <input type='image' src='../images/buttonIcon/unhearting.png' class='icon heart' data-hearting='false'
                        data-heartUser='" . htmlspecialchars($_SESSION['user_id']) . "' data-heartPost='" . $postUser['post_id'] . "' data-heartComment='" . $parentComment['comment_id'] . "'>
                        <span class='heart_count' data-heartPost='" . $postUser['post_id'] . "'  data-heartComment='" . $parentComment['comment_id'] . "'>0</span>
                        <input type='image' src='../images/buttonIcon/comment.png' class='icon comment' id='comment' onclick='commentModal(" . $postUser['post_id'] . ", \"comment\", " . $parentComment['comment_id'] . ")'>
                    </div>
                </div>
                ";

                // 子コメントを取得
                $parentCommentGet2 = "SELECT *
                FROM comments AS c
                INNER JOIN user_info AS info
                    ON c.comment_user = info.user_id
                WHERE parent_comment_id = :parent_comment_id";

                $stmt = $dbh->prepare($parentCommentGet2);
                $stmt->bindValue(':parent_comment_id', $parentComment['comment_id'], PDO::PARAM_INT);
                $stmt->execute();
                $parentComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 子コメントがあればcommentParent関数を呼び出す
                if (isset($parentComments)) {
                    foreach ($parentComments as $parentComment2) {
                        commentParent($parentComment, $postUser, $parentComment2, $dbh);
                    }
                }
            }
            ?>
        </div>
    </div>

    <?php include 'modal.php'; ?>
    <?php include 'footer.php'; ?>

    <script src=" ../js/timeline.js"></script>
    <script src="../js/return.js"></script>
</body>

</html>