<?php
session_start();
// db接続
require 'db_connect.php';
$dbh = getConnection();

$action = $_POST['action'] ?? '';
$follower_id = $_POST['follower_id'] ?? 0;
$follow_id = $_POST['follow_id'] ?? 0;

$params = [
    ':event' => 'follow',
    ':event_user' => $follow_id,
    ':event_receiver' => $follower_id,
];

// フォロー処理
if ($action === 'follow') {
    // followsテーブルに追加
    $stmt = $dbh->prepare("INSERT INTO follows (follower_id, follow_id) VALUES (:follower_id, :follow_id)");
    $stmt->execute([':follower_id' => $follower_id, ':follow_id' => $follow_id]);

    // eventsテーブルに追加
    $params += [':is_read' => 0];
    $stmt = $dbh->prepare("INSERT INTO events (event, event_user, event_receiver, is_read) VALUES (:event, :event_user, :event_receiver, :is_read)");
    $stmt->execute($params);
}
// フォロー解除処理
elseif ($action === 'unfollow') {
    // followsテーブルから削除
    $stmt = $dbh->prepare("DELETE FROM follows WHERE follower_id = :follower_id AND follow_id = :follow_id");
    $stmt->execute([':follower_id' => $follower_id, ':follow_id' => $follow_id]);

    // eventsテーブルから削除
    $stmt = $dbh->prepare("DELETE FROM events WHERE event = :event AND event_user = :event_user AND event_receiver = :event_receiver");
    $stmt->execute($params);
}
// フォロー状態を確認
elseif ($action === 'is_following') {
    // 対象のフォロー状態を確認し、1件以上であればtrueを返す
    $stmt = $dbh->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = :follower_id AND follow_id = :follow_id");
    $stmt->execute([':follower_id' => $follower_id, ':follow_id' => $follow_id]);
    $is_following = $stmt->fetchColumn() > 0;
    echo json_encode(['is_following' => $is_following]);
} else {
    echo json_encode(['error' => 'Invalid action']);
}
