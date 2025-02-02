// 戻るボタン用関数
function Return(detailCount) {
    // もしdetail.phpに2回以上遷移していたら
    if (detailCount >= 2) {
        // detail.phpに遷移した回数分戻る
        history.go(-detailCount);
    } 
    // それ以外は通常の戻る動作
    else {
        history.back();
    }
}