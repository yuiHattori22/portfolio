const twoDimension = document.getElementById('two_dimension');
const threeDimension = document.getElementById('three_dimension');
const twoElem = document.querySelector('.two');
const threeElem = document.querySelector('.three');

// ジャンル：2次元を選択した場合
twoDimension.addEventListener('change', () => {
	// ラジオボタンの場合
	if (twoDimension.type === 'radio') {
		// 2次元用のボタンのみ表示
		if (twoDimension.checked) {
			twoElem.style.display = 'block';
			threeElem.style.display = 'none';
		}
	} 
	// チェックボックスの場合
	else if (twoDimension.type === 'checkbox') {
		// チェックされたら
        if (twoDimension.checked) {
			// 2次元用ボタンを表示
            twoElem.style.display = 'block';
        } 
		// 解除されたら
		else {
			// 2次元用ボタンを非表示
            twoElem.style.display = 'none';

			// 子要素のチェックボックスをすべて解除
			const twoCheckbox = twoElem.querySelectorAll('input[type="checkbox"]');
            twoCheckbox.forEach(checkbox => {
				checkbox.checked = false;
			})
        }
    }
});

// ジャンル：3次元を選択した場合
// 2次元用と構造は同じ
threeDimension.addEventListener('change', () => {
	if (threeDimension.type === 'radio') {
		if (threeDimension.checked) {
			twoElem.style.display = 'none';
			threeElem.style.display = 'block';
		}
	} else if (threeDimension.type === 'checkbox') {
        if (threeDimension.checked) {
            threeElem.style.display = 'block';
        } else {
            threeElem.style.display = 'none';
			const threeCheckbox = threeElem.querySelectorAll('input[type="checkbox"]');
            threeCheckbox.forEach(checkbox => {
				checkbox.checked = false;
			})
        }
    }
});