<!-- 投稿ボタン -->
<form action="post.php" method="post" class="post_form">
    <button type="submit" name="post_btn" id="post_btn" class="post_btn">布教する！</button>
</form>

<!-- タイムライン表示 -->
<div class="timeline">
    <?php
    // 投稿を表示
    foreach ($posts as $post) {
        // グループ名が登録されている場合
        if ($post['group_name']) {
            // グループ画像が登録されている場合、どちらも表示
            if ($post['group_image']) {
                $group = "グループ名:" . htmlspecialchars($post['group_name']) . "<br>
                    <img src='" . htmlspecialchars($post['group_image']) . "' alt='画像' class='group_image' onclick='showModal(this.src, event)'>";
            }
            // それ以外は名前のみ表示
            else {
                $group = "グループ名:" . htmlspecialchars($post['group_name']) . "<br>";
            }
        } else {
            $group = '';
        }

        $appeal_movie = '';
        for ($i = 1; $i <= 4; $i++) {
            // オススメ動画等が登録されている場合、登録されている個数分表示させる
            if ($post["appeal_movie{$i}"]) {
                $appeal_movie .= "おすすめ動画等:" . htmlspecialchars($post["appeal_movie{$i}"]) . "<br>";
            } else {
                break;
            }
        }

        echo "
        <div class='post'>
            <!-- 投稿をクリック時にdetail.phpに遷移 -->
            <a href='detail.php?post_id=" . htmlspecialchars($post['post_id']) . "'>
                <!-- アイコンもしくは名前をクリック時にprof.phpに遷移 -->
                <form method='post' action='prof.php'>
                    <div class='timeline_prof'>
                        <button type='submit' name='user_id' value='" . htmlspecialchars($post['user_id']) . "' class='prof_btn'>
                            <img src='" . htmlspecialchars($post['user_icon']) . "' alt='画像' class='timeline_icon'>
                        </button>
                        <button type='submit' name='user_id' value='" . htmlspecialchars($post['user_id']) . "' class='prof_btn'>
                            <p class='timeline_username'>" . htmlspecialchars($post['user_name']) . "</p>
                        </button>
                    </div>
                </form>
                <!-- 投稿の詳細情報を表示 -->
                <div class='timeline_post'>
                    <img src='" . htmlspecialchars($post['main_image']) . "' alt='画像' class='main_image' onclick='showModal(this.src, event)'>
                    <p>名前:" . htmlspecialchars($post['oshi_name']) . "<br>
                    ジャンル:{$post['genre']}<br>
                    {$group}
                    推しポイント:" . htmlspecialchars($post['appeal_point']) . "<br>
                    {$appeal_movie}</p>
                    <p class='timeline_datetime'>{$post['create_datetime']}</p>
                </div>
            </a>
            <!-- 投稿への反応ボタン -->
            <div class='post_icon'>
                <!-- data-heartingの値によっていいね状態を変更 -->
                <input type='image' src='../images/buttonIcon/unhearting.png' class='icon heart' data-hearting='false' data-heartUser='" . htmlspecialchars($_SESSION['user_id']) . "' data-heartPost='{$post['post_id']}'>
                <!-- いいね数を表示 -->
                <span class='heart_count' data-heartPost='{$post['post_id']}'>0</span>
                <!-- クリック時にコメントモーダルを表示 -->
                <input type='image' src='../images/buttonIcon/comment.png' class='icon comment' onclick='commentModal({$post['post_id']}, \"post\")'>
            </div>
        </div>";
    }
    ?>
</div>