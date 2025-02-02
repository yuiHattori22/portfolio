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

<!-- 自身のプロフページへ遷移できるヘッダー -->
<header>
    <form method="post" action="prof.php">
        <button type="submit" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" class="prof_btn">
            <img src="<?php echo $_SESSION['user_icon']; ?>" alt="画像" class="prof_icon">
        </button>
    </form>
    <form method="post" class="logout_form">
        <button type="submit" name="logout" class="logout_btn">ログアウト</button>
    </form>
</header>

<script src="../js/return.js"></script>