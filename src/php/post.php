<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/croppie.css">
</head>

<body>
    <?php include 'header_return.php'; ?>
    <form id="post_form" action="timeline.php" method="post" enctype="multipart/form-data">
        <!-- メイン画像アップロード・表示用コンテナ -->
        <div class="main_image_container">
            <label class="image_label" id="image_label_1">
                <input class="hidden" type="file" accept="image/png,image/jpeg,image/gif" onchange="cropImage('2/3', 150, 'square', '1', event)">推しのお写真
            </label>
            <!-- croppieで切り抜いた画像を表示 -->
            <img id="cropped_image_1" src="" alt="" class="cropped_image" id="next_crop">
            <!-- croppieで切り抜いた画像データを保持 -->
            <input type="hidden" id="cropped_image_hidden_1" name="main_image">
            <!-- croppieで切り抜きを行う際のモーダル表示 -->
            <div id="image_modal_1" class="image_modal">
                <div class="modal_content" id="modal_content_1">
                    <div id="croppie_1" style="width: 100%; height: 100%;"></div>
                    <button id="crop_button_1" style="display: none;">完了</button>
                </div>
            </div>
        </div>
        <!-- 布教対象の名前入力欄 -->
        <p>推しの名前 <input type="text" value="" id="oshi_name" name="oshi_name"></p>

        <!-- 性別選択ボタン -->
        <div class="padding">
            <label class="radio"><input class="hidden" type="radio" value="女性" name="oshi_sex"><span>女性</span></label>
            <label class="radio"><input class="hidden" type="radio" value="男性" name="oshi_sex"><span>男性</span></label>
            <label class="radio"><input class="hidden" type="radio" value="その他" name="oshi_sex"><span>その他</span></label>
        </div>

        <!-- ジャンル選択ボタン -->
        <div class="padding">
            <label class="radio"><input class="hidden" type="radio" value="2次元" name="dimension" id="two_dimension"><span>2次元</span></label>
            <label class="radio"><input class="hidden" type="radio" value="3次元" name="dimension" id="three_dimension"><span>3次元</span></label>
            <div class="two padding">
                <label class="radio"><input class="hidden" type="radio" value="アニメ" name="genre"><span>アニメ</span></label>
                <label class="radio"><input class="hidden" type="radio" value="ゲーム" name="genre"><span>ゲーム</span></label>
                <label class="radio"><input class="hidden" type="radio" value="VTuber" name="genre"><span>VTuber</span></label>
                <label class="radio"><input class="hidden" type="radio" value="その他" name="genre"><span>その他</span></label>
            </div>
            <div class="three padding">
                <label class="radio"><input class="hidden" type="radio" value="アイドル" name="genre"><span>アイドル</span></label>
                <label class="radio"><input class="hidden" type="radio" value="アーティスト" name="genre"><span>アーティスト</span></label>
                <label class="radio"><input class="hidden" type="radio" value="モデル" name="genre"><span>モデル</span></label>
                <label class="radio"><input class="hidden" type="radio" value="女優/俳優" name="genre"><span>女優/俳優</span></label>
                <label class="radio"><input class="hidden" type="radio" value="声優" name="genre"><span>声優</span></label>
                <label class="radio"><input class="hidden" type="radio" value="歌い手" name="genre"><span>歌い手</span></label>
                <label class="radio"><input class="hidden" type="radio" value="YouTuber" name="genre"><span>YouTuber</span></label>
                <label class="radio"><input class="hidden" type="radio" value="その他" name="genre"><span>その他</span></label>
            </div>
        </div>
        <!-- グループ画像アップロード・表示用コンテナ -->
        <!-- 構造はメイン画像と同じ -->
        <div class="group_image_container">
            <label class="image_label" id="image_label_2">
                <input class="hidden" type="file" name="group_image" accept="image/png,image/jpeg,image/gif"
                    onchange="cropImage('5/3', 300, 'square', '2', event)">グループ写真
            </label>
            <img id="cropped_image_2" src="" alt="" class="cropped_image" id="next_crop">
            <input type="hidden" id="cropped_image_hidden_2" name="group_image">
            <div id="image_modal_2" class="image_modal">
                <div class="modal_content" id="modal_content_2">
                    <div id="croppie_2" style="width: 100%; height: 100%;"></div>
                    <button id="crop_button_2" style="display: none;">完了</button>
                </div>
            </div>
        </div>
        <!-- グループ名入力欄 -->
        <p>グループ名(あれば) <input type="text" name="group_name"></p>
        <!-- アピールポイント入力欄 -->
        <p>推しポイント <textarea id="appeal_point" name="appeal_point"></textarea></p>
        <!-- 布教動画等入力欄 -->
        <div id="increase_form" class="increase_form">
            <p>布教動画等
                <textarea name="appeal_movie1"></textarea>
            </p>
        </div>
        <!-- 入力欄を増やすボタン -->
        <button id="increase">+</button>
        <!-- 投稿ボタン -->
        <button type="submit" name="post_btn" id="post_btn" class="post_input_btn" disabled>投稿する</button>
    </form>

    <script src="../js/check.js"></script>
    <script src="../js/post.js"></script>
    <script src="../js/croppie.js"></script>
    <script src="../js/image_crop.js"></script>
</body>

</html>