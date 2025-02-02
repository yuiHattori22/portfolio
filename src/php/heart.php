<?php
session_start();
// db接続
require 'db_connect.php';
$dbh = getConnection();

$action = $_POST['action'] ?? '';
$target = $_POST['target'] ?? '';
$heart_user = $_POST['heart_user'] ?? 0;
$heart_post = $_POST['heart_post'] ?? 0;
$heart_comment = $_POST['heart_comment'] ?? 0;

// 対象投稿のuser_idを取得
$query = "SELECT user_id
FROM posts
WHERE post_id = :post_id";

$stmt = $dbh->prepare($query);
$stmt->bindValue(':post_id', $_POST['heart_post'], PDO::PARAM_STR);
$stmt->execute();
$target_user = $stmt->fetch(PDO::FETCH_ASSOC);

try {
    // 対象が投稿の場合
    if ($target === 'post') {
        // いいね処理
        if ($action === 'heart') {
            $params = [
                ':event' => 'heart',
                ':event_user' => $heart_user,
                ':event_receiver' => $target_user['user_id'],
                ':post_id' => $heart_post,
                ':is_read' => 0,
            ];

            // heartsテーブルに追加
            $stmt = $dbh->prepare("INSERT INTO hearts (heart_user, heart_post) VALUES (:heart_user, :heart_post)");
            $stmt->execute([':heart_user' => $heart_user, ':heart_post' => $heart_post]);

            // eventsテーブルに追加
            $stmt = $dbh->prepare("INSERT INTO events (event, event_user, event_receiver, post_id, is_read) VALUES (:event, :event_user, :event_receiver, :post_id, :is_read)");
            $stmt->execute($params);
            echo json_encode(['status' => 'success', 'action' => 'heart']);
        }
        // いいね解除処理
        elseif ($action === 'unheart') {
            $params = [
                ':event' => 'heart',
                ':event_user' => $heart_user,
                ':event_receiver' => $target_user['user_id'],
                ':post_id' => $heart_post,
            ];

            // heartsテーブルから削除
            $stmt = $dbh->prepare("DELETE FROM hearts WHERE heart_user = :heart_user AND heart_post = :heart_post AND heart_comment IS NULL");
            $stmt->execute([':heart_user' => $heart_user, ':heart_post' => $heart_post]);

            // eventsテーブルから削除
            $stmt = $dbh->prepare("DELETE FROM events WHERE event = :event AND event_user = :event_user AND event_receiver = :event_receiver AND post_id = :post_id");
            $stmt->execute($params);
            echo json_encode(['status' => 'success', 'action' => 'unheart']);
        }
        // いいね状態を確認
        elseif ($action === 'is_hearting') {
            // 対象のいいね状態を確認し、1件以上であればtrueを返す
            $stmt = $dbh->prepare("SELECT COUNT(*) FROM hearts WHERE heart_user = :heart_user AND heart_post = :heart_post AND heart_comment IS NULL");
            $stmt->execute([':heart_user' => $heart_user, ':heart_post' => $heart_post]);
            $is_hearting = $stmt->fetchColumn() > 0;
            echo json_encode(['is_hearting' => $is_hearting]);
        }
        // いいね数をカウント
        elseif ($action === 'get_heart_count') {
            // 対象に対していいねを行っているレコードを全てカウント
            $stmt = $dbh->prepare("SELECT COUNT(*) FROM hearts WHERE heart_post = :heart_post AND heart_comment IS NULL");
            $stmt->execute([':heart_post' => $heart_post]);
            $heart_count = $stmt->fetchColumn();
            echo json_encode(['heart_count' => $heart_count]);
        } else {
            echo json_encode(['error' => 'Invalid action']);
        }
    }
    // 対象がコメントの場合
    elseif ($target === 'comment') {
        // いいね処理
        if ($action === 'heart') {
            $params = [
                ':event' => 'heart',
                ':event_user' => $heart_user,
                ':event_receiver' => $target_user['user_id'],
                ':post_id' => $heart_post,
                ':comment_id' => $heart_comment,
            ];

            // heartsテーブルに追加
            $stmt = $dbh->prepare("INSERT INTO hearts (heart_user, heart_post, heart_comment) VALUES (:heart_user, :heart_post, :heart_comment)");
            $stmt->execute([':heart_user' => $heart_user, ':heart_post' => $heart_post, ':heart_comment' => $heart_comment]);

            // eventsテーブルに追加
            $stmt = $dbh->prepare("INSERT INTO events (event, event_user, event_receiver, post_id, comment_id, is_read) VALUES (:event, :event_user, :event_receiver, :post_id, :comment_id, :is_read)");
            $params += [':is_read' => 0];
            $stmt->execute($params);
            echo json_encode(['status' => 'success', 'action' => 'heart']);
        }
        // いいね解除処理
        elseif ($action === 'unheart') {
            $params = [
                ':event' => 'heart',
                ':event_user' => $heart_user,
                ':event_receiver' => $target_user['user_id'],
                ':post_id' => $heart_post,
                ':comment_id' => $heart_comment,
            ];

            // heartsテーブルから削除
            $stmt = $dbh->prepare("DELETE FROM hearts WHERE heart_user = :heart_user AND heart_post = :heart_post AND heart_comment = :heart_comment");
            $stmt->execute([':heart_user' => $heart_user, ':heart_post' => $heart_post, ':heart_comment' => $heart_comment]);

            // eventsテーブルから削除
            $stmt = $dbh->prepare("DELETE FROM events WHERE event = :event AND event_user = :event_user AND event_receiver = :event_receiver AND post_id = :post_id AND comment_id = :comment_id");
            $stmt->execute($params);
            echo json_encode(['status' => 'success', 'action' => 'unheart']);
        }
        // いいね状態を確認
        elseif ($action === 'is_hearting') {
            // 対象のいいね状態を確認し、1件以上であればtrueを返す
            $stmt = $dbh->prepare("SELECT COUNT(*) FROM hearts WHERE heart_user = :heart_user AND heart_post = :heart_post AND heart_comment = :heart_comment");
            $stmt->execute([':heart_user' => $heart_user, ':heart_post' => $heart_post, ':heart_comment' => $heart_comment]);
            $is_hearting = $stmt->fetchColumn() > 0;
            echo json_encode(['is_hearting' => $is_hearting]);
        }
        // いいね数をカウント
        elseif ($action === 'get_heart_count') {
            // 対象に対していいねを行っているレコードを全てカウント
            $stmt = $dbh->prepare("SELECT COUNT(*) FROM hearts WHERE heart_post = :heart_post AND heart_comment = :heart_comment");
            $stmt->execute([':heart_post' => $heart_post, ':heart_comment' => $heart_comment]);
            $heart_count = $stmt->fetchColumn();
            echo json_encode(['heart_count' => $heart_count]);
        } else {
            echo json_encode(['error' => 'Invalid action']);
        }
    } else {
        echo json_encode(['error' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'データベースエラー: ' . $e->getMessage()]);
}
