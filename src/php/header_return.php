<?php
try {
    // ログアウトボタンが押下された場合
    if (isset($_POST['logout'])) {
        // セッション情報を削除しログインページへ遷移
        session_destroy();
        header('Location: login.php');
        exit();
    }
} catch (Exception $e) {
    echo 'エラーが発生しました。', $e->getMessage(), $e->getCode();
}
?>

<!-- 前ページへ遷移できるヘッダー -->
<header>
    <input type="image" src="../images/buttonIcon/return.png" alt="戻る" class="return" onclick="history.back();">
    <form method=" post" class="logout_form" action="timeline.php">
        <button type="submit" name="logout" class="logout_btn">ログアウト</button>
    </form>
</header>

<script src="../js/return.js"></script>