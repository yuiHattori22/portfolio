<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>検索</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'header_return.php'; ?>

    <form method="post" action="result.php">
        <!-- フリーワード検索欄 -->
        <p>フリーワード<input type="text" name="freeword"></p>
        <!-- 性別をチェックボックスで選択 -->
        <div class="padding">
            <label class="checkbox"><input class="hidden" type="checkbox" value="女性" name="oshi_sex"><span>女性</span></label>
            <label class="checkbox"><input class="hidden" type="checkbox" value="男性" name="oshi_sex"><span>男性</span></label>
            <label class="checkbox"><input class="hidden" type="checkbox" value="その他" name="oshi_sex"><span>その他</span></label>
        </div>
        <!-- ジャンルをチェックボックスで選択 -->
        <div class="padding">
            <label class="checkbox"><input class="hidden" type="checkbox" value="2次元" name="dimension" id="two_dimension"><span>2次元</span></label>
            <label class="checkbox"><input class="hidden" type="checkbox" value="3次元" name="dimension" id="three_dimension"><span>3次元</span></label>
            <div class="two padding">
                <label class="checkbox"><input class="hidden" type="checkbox" value="アニメ" name="genre"><span>アニメ</span></label>
                <label class="checkbox"><input class="hidden" type="checkbox" value="ゲーム" name="genre"><span>ゲーム</span></label>
                <label class="checkbox"><input class="hidden" type="checkbox" value="VTuber" name="genre"><span>VTuber</span></label>
                <label class="checkbox"><input class="hidden" type="checkbox" value="その他" name="genre"><span>その他</span></label>
            </div>
            <div class="three padding">
                <label class="checkbox"><input class="hidden" type="checkbox" value="アイドル" name="genre"><span>アイドル</span></label>
                <label class="checkbox"><input class="hidden" type="checkbox" value="アーティスト" name="genre"><span>アーティスト</span></label>
                <label class="checkbox"><input class="hidden" type="checkbox" value="モデル" name="genre"><span>モデル</span></label>
                <label class="checkbox"><input class="hidden" type="checkbox" value="女優/俳優" name="genre"><span>女優/俳優</span></label>
                <label class="checkbox"><input class="hidden" type="checkbox" value="声優" name="genre"><span>声優</span></label>
                <label class="checkbox"><input class="hidden" type="checkbox" value="歌い手" name="genre"><span>歌い手</span></label>
                <label class="checkbox"><input class="hidden" type="checkbox" value="YouTuber" name="genre"><span>YouTuber</span></label>
                <label class="checkbox"><input class="hidden" type="checkbox" value="その他" name="genre"><span>その他</span></label>
            </div>
        </div>

        <button class="search_btn" name="search_btn">検索</button>
    </form>
    <?php include 'footer.php'; ?>

    <script src="../js/check.js"></script>
</body>

</html>