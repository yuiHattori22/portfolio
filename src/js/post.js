const increase = document.getElementById('increase');
const postBtn = document.getElementById('post_btn');
const nameInput = document.getElementById('oshi_name');
const sexInputs = document.querySelectorAll('input[name="oshi_sex"]');
const genreInputs = document.querySelectorAll('input[name="genre"]');
const appealInput = document.getElementById('appeal_point');

let formCounter = 1;

// +ボタンをクリックした場合
// 1番下以外の入力欄を削除した場合、nameの値が被ってしまうため修正が必要
increase.addEventListener('click', (event) => {
	event.preventDefault();

	// 入力欄の数を保持
	formCounter++;

	// 新しい入力欄を作成
	const newForm = document.createElement('textarea');
	newForm.name = `appeal_movie${formCounter}`;

	const newParagraph = document.createElement('p');
	newParagraph.textContent = '布教動画等 ';

	const delBtn = document.createElement('button');
	delBtn.textContent = '削除';
	delBtn.classList.add('del_btn');

	newParagraph.appendChild(newForm);
	newParagraph.appendChild(delBtn);
	document.getElementById('increase_form').appendChild(newParagraph);

	// 入力欄が4つになったら
	if (formCounter >= 4) {
		// +ボタンを削除
		increase.style.display = 'none';
	}

	// 削除ボタンをクリックした場合
	delBtn.addEventListener('click', () => {
		formDel(newParagraph, increase);
	});
});

// 入力欄削除用関数
function formDel(rmElem, increase) {
	rmElem.remove();
	if (increase.style.display == 'none') {
		increase.style.display = 'block';
	}
	// カウンターの数を減らす
	formCounter--;
}

// 入力チェック用関数
function checkFormValidity() {
	// 入力欄が空欄ではないこと
	const isNameFilled = nameInput.value.trim() !== '';
	const isAppealFilled = appealInput.value.trim() !== '';
	// 少なくとも一つが選択されていること
	const isSexSelected = Array.from(sexInputs).some((input) => input.checked);
	const isGenreSelected = Array.from(genreInputs).some((input) => input.checked);

	// 必須項目全てが入力されるまでボタンを非活性
	postBtn.disabled = !(isNameFilled && isSexSelected && isGenreSelected && isAppealFilled);
}

// 各入力欄に値が入るたびcheckFormValidity関数を呼び出す
nameInput.addEventListener('input', checkFormValidity);
sexInputs.forEach((input) => input.addEventListener('change', checkFormValidity));
genreInputs.forEach((input) => input.addEventListener('change', checkFormValidity));
appealInput.addEventListener('input', checkFormValidity);