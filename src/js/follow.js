// domがロードされた時点でフォロー状態を確認
document.addEventListener('DOMContentLoaded', () => {
    const followBtn = document.getElementById('follow');

    // フォローボタンがあれば
    if(followBtn){
        const followerId = followBtn.dataset.followerid;
        const followId = followBtn.dataset.followid;
        
        let isFollowing = null;

        // フォロー状態の確認
        fetch('follow.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'is_following',
                follower_id: followerId,
                follow_id: followId
            })
        })
        .then(response => response.json())
        .then(data => {
            // 受け取ったデータによってフォロー状態を変更
            isFollowing = data.is_following;
            followBtn.textContent = isFollowing ? 'フォロー中' : 'フォローする';
            followBtn.dataset.following = isFollowing ? 'true' : 'false';
        });

        // フォローボタンが押下された場合
        followBtn.addEventListener('click', () => {
            // 状態によってactionを変える
            const action = isFollowing ? 'unfollow' : 'follow';

            // 現在のフォロー状態にテーブルを更新
            fetch('follow.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: action,
                    follower_id: followerId,
                    follow_id: followId
                })
            });

            // 元のフォロー状態から反転させる
            isFollowing = !isFollowing;
            followBtn.textContent = isFollowing ? 'フォロー中' : 'フォローする';
            followBtn.dataset.following = isFollowing ? 'true' : 'false';
        });
    }
});