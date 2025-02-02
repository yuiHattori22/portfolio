// croppie呼び出し用関数
function cropImage(aspectRatio, resultWidth, type, elemNum, event) {
	// コンストラクタを呼び出し引数を渡す
	const imageCroppie = new ImageCroppie(elemNum, aspectRatio);
	// croppie開始
	imageCroppie.inputImage(event, type);
	
	// 完了ボタンが押下された場合
	document.getElementById('crop_button_' + elemNum).addEventListener('click', function (event) {
		event.preventDefault();
		// トリミングされた画像を出力
		imageCroppie.outputImage(resultWidth);
	});
}

class ImageCroppie {
	constructor(elemNum, aspectRatio){
		// 切り抜き時のアスペクト比
		this.aspectRatio = eval(aspectRatio);
		this.cropped_image = document.getElementById('cropped_image_' + elemNum);
        this.label = document.getElementById('image_label_' + elemNum);
		this.crop_button = document.getElementById('crop_button_' + elemNum);
        this.image_modal = document.getElementById('image_modal_' + elemNum);
		this.croppie = document.getElementById('croppie_' + elemNum);
		this.hiddenInput = document.getElementById('cropped_image_hidden_' + elemNum);
        this.fileInput = this.label.querySelector('input[type="file"]');
		this.croppieInstance = null;

		// 切り抜き後の画像をクリックした場合
        this.cropped_image.addEventListener('click', () => {
			// インスタンスを破棄し再作成
            this.croppieInstance.destroy();
            this.croppieInstance = null;
			// ファイル入力ボタンをクリック処理
			this.fileInput.click();
		});
	}

	// croppieを起動する
	inputImage(event, type){
		const file = event.target.files[0];
		const screenWidth = window.innerWidth;
		const screenHeight = window.innerHeight;
		// 外枠を全体の80%の大きさで表示
		let boundaryWidth = screenWidth * 0.8;
    	let boundaryHeight = screenHeight * 0.8;

		// 切り抜きの横幅をモーダルの80%に調整
		let viewportWidth = boundaryWidth * 0.8;
		// 横幅を基準にアスペクト比で縦幅を決定
		let viewportHeight = viewportWidth / this.aspectRatio;
		
		// 画面高さに収まらない場合は高さを基準に再計算
		if (viewportHeight > boundaryHeight * 0.8) {
			viewportHeight = boundaryHeight * 0.8;
			viewportWidth = viewportHeight * this.aspectRatio;
		}
	
		// ファイルが正しく取得できていれば
		if(file){
			const reader = new FileReader();

			// INFO: 外のthisを保持したい場合はアロー関数使用
			// ファイルが正常に読み込まれたら
			reader.onload = (e) => {
				// インスタンスが破棄されていなければ破棄し再作成
				if (this.croppieInstance != null) {
					this.croppieInstance.destroy();
                    this.croppieInstance = null;
				}

				// 切り抜き操作や設定用のインスタンス作成
				this.croppieInstance = new Croppie(this.croppie, {
					// 読み込んだ画像が対象ファイル
					url: e.target.result,
					// 切り抜き幅や形の設定
					viewport: {
						width: viewportWidth,
						height: viewportHeight,
						type: type
					},
					// 外枠の大きさの決定
					boundary: {
						width: boundaryWidth,
						height: boundaryHeight
					}
				})
				// モーダルを表示
				this.image_modal.style.display = 'flex';
				this.crop_button.style.display = 'inline-block';
			}
			// ファイルデータを読み込む
			reader.readAsDataURL(file);
		}
	}

	// 切り抜いた画像を出力
	outputImage(result_width){
		// インスタンスが正常に格納されていれば
		if (this.croppieInstance) {
			// 切り抜いた結果に対して出力用の設定を適用
			this.croppieInstance.result({
				type: 'base64',
				format: 'png',
				size: { width: result_width, height: Math.floor(result_width / (this.aspectRatio)) }
			}).then((croppedImage) => {
				// 設定が適用されたインスタンスに対して再度設定
				this.croppieInstance.result({
					type: 'base64',
					format: 'png',
					// 元の画像のサイズを保持
					size: 'original'
				}).then((originalCroppedImage) => {
					// 画像を出力
					this.cropped_image.src = croppedImage;
					this.cropped_image.style.display = 'block';
					// モーダルを非表示
					this.image_modal.style.display = 'none';
					// ラベルを非表示
					this.label.style.display  = 'none';
					// 切り抜き後の画像データを格納
					this.hiddenInput.value = originalCroppedImage;
				});
			});
		}
	}
}