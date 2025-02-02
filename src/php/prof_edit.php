<?php
session_start();
require 'db_connect.php';
$dbh = getConnection();

// 編集ボタンが押下された場合
if (isset($_POST['edit'])) {
    $user_icon = $_POST['user_icon'];
    $user_name = $_POST['user_name'];
    $user_profile = $_POST['user_profile'];
} else {
    echo 'ユーザー情報が取得できませんでした。';
    die;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/croppie.css">
</head>

<body>
    <?php include 'header_return.php'; ?>
    <form method="post" action="prof.php">
        <!-- アイコン変更 -->
        <div class="prof_image_container">
            <!-- アイコン画像を表示(変更を行ったら変更) -->
            <img src="<?php echo htmlspecialchars($user_icon); ?>" alt="画像" class="prof_image cropped_image" id="cropped_image_1">
            <label class="prof_label" id="image_label_1">
                <input type="file" accept="image/png,image/jpeg,image/gif" onchange="cropImage('1/1', 200, 'circle', '1', event)">
            </label>
            <img src="../images/buttonIcon/camera.png" class="camera_icon icon_display">
            <!-- croppieで切り抜いた画像データを保持 -->
            <input type="hidden" id="cropped_image_hidden_1" name="user_icon" value="<?php echo htmlspecialchars($user_icon); ?>">
            <!-- croppieで切り抜きを行う際のモーダル表示 -->
            <div id="image_modal_1" class="image_modal">
                <div class="modal_content" id="modal_content_1">
                    <div id="croppie_1" style="width: 100%; height: 100%;"></div>
                    <button id="crop_button_1" style="display: none;">完了</button>
                </div>
            </div>
        </div>
        <!-- 名前の変更 -->
        <input type="text" value="<?php echo htmlspecialchars($user_name); ?>" name="user_name">名前
        <!-- プロフィールの変更 -->
        <input type="text" value="<?php echo htmlspecialchars($user_profile); ?>" name="user_profile">プロフィール
        <button name="edit">編集を完了する</button>
    </form>

    <script src="../js/croppie.js"></script>
    <script src="../js/image_crop.js"></script>
</body>

</html>