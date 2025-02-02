<!-- 画像拡大表示用のモーダル -->
<div id="image_modal" class="image_modal" onclick="closeModal()">
    <img id="modal_image" src="" alt="画像">
</div>

<!-- コメント用のモーダル -->
<div id="comment_modal" class="comment_modal">
    <form method="post" action="detail.php">
        <input type="text" class="comment_input" name="comment_input" placeholder="コメントする" id="comment_input">
        <!-- コメントの対象と対象のidをそれぞれname,valueに格納し送信 -->
        <input type="hidden" name="" class="comment_post" value="">
        <button name="comment_btn" id="comment_btn">コメントする</button>
        <button id="cancel">キャンセル</button>
    </form>
</div>