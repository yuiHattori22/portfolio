const imgModal = document.getElementById('image_modal');

// 画像モーダルを表示
function showModal(src, event) {
	event.preventDefault();

	const modalImg = document.getElementById('modal_image');
	modalImg.src = src;
	imgModal.style.display = 'flex';
}

// 画像モーダルを閉じる
function closeModal() {
	imgModal.style.display = 'none';
}

// コメントモーダルを表示
function commentModal(postId, target, commentId = null) {
	const commentModal = document.getElementById('comment_modal');
	commentModal.style.display = 'flex';

	const commentPost = commentModal.querySelector('.comment_post');
	let value = postId;

	// コメントに対するコメントの場合、comment_idも加えて送信
	commentPost.value = postId;
	if(commentId != null){
		value += ',' + commentId;
	}
	commentPost.value = value;
	commentPost.name = target;

	// オーバーレイを作成
	const overlay = document.createElement('div');
    overlay.classList.add('overlay');
    document.body.appendChild(overlay);

	const commentInput = document.getElementById("comment_input");
    const commentBtn = document.getElementById("comment_btn");
	
    commentBtn.disabled = true;

    // 未入力状態ではコメントボタンが押下できないように処理
    commentInput.addEventListener("input", () => {
        if (commentInput.value.trim() !== "") {
            commentBtn.disabled = false;
        } else {
            commentBtn.disabled = true;
        }
    });

	// モーダルを閉じる
	document.getElementById('cancel').addEventListener('click', (event) => {
		event.preventDefault();
		commentModal.style.display = 'none';
        overlay.remove();
	})
}

// DOMのロード後、全ての投稿のいいねの状態を取得
document.addEventListener('DOMContentLoaded', () => {
    const hearting = document.querySelectorAll('.heart');
	if(hearting){
		hearting.forEach(heartBtn => {
			const heartUser = heartBtn.dataset.heartuser;
			const heartPost = heartBtn.dataset.heartpost;
			const heartComment = heartBtn.dataset.heartcomment;
			const heartPostCount = document.querySelectorAll(`.heart_count[data-heartpost="${heartPost}"]`);
			const heartCommentCount = document.querySelectorAll(`.heart_count[data-heartcomment="${heartComment}"]`);

			let isHearting = 'false';
			let target = '';

			// いいね対象が投稿かコメントか判定
			if(heartPost && heartComment){
				target = 'comment';
			} else if(heartPost){
				target = 'post';
			}
			
			if (!target || !heartPost) {
				console.error('対象を取得できませんでした');
				return;
			}

			let params = new URLSearchParams({
				action: 'is_hearting',
				target: target,
				heart_user: heartUser,
				heart_post: heartPost,
			});

			// 対象がコメントの場合はcoment_idも一緒に送信
			if (heartComment) {
				params.append('heart_comment', heartComment);
			}
			
			// 各投稿のいいねの状態を取得
			fetch('heart.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: params
			})
			.then(response => response.json())
			.then(data => {
				// レスポンスからいいねの状態を判断し、アイコンを変更
				isHearting = data.is_hearting;
				heartBtn.dataset.hearting = isHearting ? 'true' : 'false';
				heartBtn.src = isHearting ? '../images/buttonIcon/hearting.png' : '../images/buttonIcon/unhearting.png';
			});

			// いいね数を取得するための関数
            function updateHeartCount() {
                let countParams = new URLSearchParams({
                    action: 'get_heart_count',
					target: target,
                    heart_post: heartPost,
                });

				// 対象がコメントの場合はcoment_idも一緒に送信
				if (heartCommentCount) {
					countParams.append('heart_comment', heartComment);
				}

				// いいね数を取得
                fetch('heart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: countParams
                })
                .then(response => response.json())
                .then(data => {
					// いいねされている投稿であれば件数を表示
                    if (data.heart_count !== undefined) {
						if(target === 'post'){
							heartPostCount[0].textContent = data.heart_count;
						} else if (target === 'comment'){
							heartCommentCount[0].textContent = data.heart_count;
						}
                    }
                });
            }

			updateHeartCount();

			// いいねボタンが押されたら
			heartBtn.addEventListener('click', (event) => {
				event.preventDefault();
				// ボタンの状態によってactionの値を変更
				params.set('action', isHearting ? 'unheart' : 'heart');			

				fetch('heart.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: params
				})
				.then(response => response.json())
                .then(() => {
					// ボタン押下時の状態から反転させる
                    isHearting = !isHearting;
                    heartBtn.dataset.hearting = isHearting ? 'true' : 'false';
                    heartBtn.src = isHearting ? '../images/buttonIcon/hearting.png' : '../images/buttonIcon/unhearting.png';
                });
				// いいね数を更新
				updateHeartCount();
			});
		});
    }
})