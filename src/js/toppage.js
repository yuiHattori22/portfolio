// フッターのボタンを取得
document.querySelectorAll('footer form').forEach(form => {
    // フォームが送信されたら
    form.addEventListener('submit', function(event) {
        // 遷移先と現在のurlを取得
        const action = this.action;
        const currentPage = window.location.pathname;

        // 現在のurlが遷移先と同じ場合
        if (action.endsWith(currentPage)) {
            // ページの先頭へスクロール
            event.preventDefault();
            window.scroll({
                top: 0,
                behavior: "smooth"});
        }
    });
});