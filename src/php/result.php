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
    <?php include 'header_return.php'; ?>
    <form action="post.php" method="post" class="post_form">
        <button type="submit" name="post_btn" id="post_btn" class="post_btn">布教する！</button>
    </form>
    <?php
    try {
        // db接続
        require 'db_connect.php';
        $dbh = getConnection();

        // 検索ボタンが押下された(正常に遷移された)場合
        if (isset($_POST['search_btn'])) {
            // チェックボックスが選択されていた場合は配列に、それ以外は空の配列(チェックボックスのため文字列として送信されることはないが、念のため文字列として送信された場合は配列に変換)
            $sex = isset($_POST['oshi_sex']) && is_array($_POST['oshi_sex']) ? $_POST['oshi_sex'] : (isset($_POST['oshi_sex']) ? [$_POST['oshi_sex']] : []);
            $genre = isset($_POST['genre']) && is_array($_POST['genre']) ? $_POST['genre'] : (isset($_POST['genre']) ? [$_POST['genre']] : []);
            // フリーワードが入力されていればスペース区切りで配列に格納し、それ以外は空の配列
            $freeword = isset($_POST['freeword']) ? preg_split('/\s+/', trim($_POST['freeword'])) : [];

            // 検索対象のカラム
            $serchColumns = ['oshi_name', 'group_name', 'appeal_point', 'appeal_movie1', 'appeal_movie2', 'appeal_movie3', 'appeal_movie4'];

            // 検索条件を満たしたpostとそのユーザー情報を取得
            $serchPost = "SELECT * FROM user_info as info 
                INNER JOIN posts as p 
                ON info.user_id = p.user_id
                WHERE 1=1";

            $params = [];

            // 性別が選択されている場合
            if (!empty($sex)) {
                // 配列の要素数の数だけ?を入れた配列を作成し、カンマ区切りの文字列に変換
                $placeholders = implode(',', array_fill(0, count($sex), '?'));
                // 性別検索用のsqlを追加し、選択された数だけプレースホルダーを作成
                $serchPost .= " AND p.oshi_sex IN ($placeholders)";
                // プレースホルダーに代入する値を格納
                $params = array_merge($params, $sex);
            }

            // ジャンルが選択されている場合
            if (!empty($genre)) {
                // 性別と同じ処理
                $placeholders = implode(',', array_fill(0, count($genre), '?'));
                $serchPost .= " AND p.genre IN ($placeholders)";
                $params = array_merge($params, $genre);
            }

            // フリーワードが入力されている場合
            if (!empty($freeword)) {
                $conditions = [];
                foreach ($freeword as $word) {
                    $subConditions = [];
                    foreach ($serchColumns as $column) {
                        // 部分一致検索するためのsqlを追加
                        $subConditions[] = "$column LIKE ?";
                        // フリーワードをワイルドカードで格納
                        $params[] = "%$word%";
                    }
                    // 検索対象のカラムの数だけsqlを追加する
                    $conditions[] = "(" . implode(' OR ', $subConditions) . ")";
                }
                // フリーワードの数だけsqlを追加する
                $serchPost .= " AND (" . implode(' OR ', $conditions) . ")";
            }

            $serchPost .= " ORDER BY p.create_datetime DESC";
            $stmt = $dbh->prepare($serchPost);

            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // リダイレクト後に保持できるようセッションに保存
            $_SESSION['results'] = $results;

            // POST処理後にリダイレクト
            header('Location: result.php');
            exit();
        }

        // 検索結果があれば$postに格納
        $posts = isset($_SESSION['results']) ? $_SESSION['results'] : [];

        include 'list.php';
        include 'modal.php';
    } catch (Exception $e) {
        echo 'エラーが発生しました。', $e->getMessage(), $e->getCode();
    }
    ?>
    </div>
    <?php include 'footer.php'; ?>

    <script src="../js/timeline.js"></script>
</body>

</html>