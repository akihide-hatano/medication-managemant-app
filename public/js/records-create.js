// 必要なHTML要素を取得
const addButton = document.getElementById('add-medication-row');
const container = document.getElementById('medication-container');
const template = document.getElementById('medication-template');

/**
 * すべての行のインデックスを更新する関数
 */
    function updateRowIndexes(){
        const rows = container.children;
        for( let i = 0; i < rows.length; i++){
            const row = rows[i];
            row.querySelector('select[name^="medications"]').name = `medications[${i}][medication_id]`;
            row.querySelector('select[name^="dosages"]').name = `medications[${i}][taken_dosage]`;
            row.querySelector('input[type="checkbox"]').name = `medications[${i}][is_completed]`;
        }
    }
/**
 * 新しい薬の行を追加する関数
 */
    function addRow() {
        const newRow = template.content.cloneNode(true);
        const rowCount = container.children.length;
        // name属性のインデックスを更新
        newRow.querySelector('select[name^="medications"]').name = `medications[${rowCount}][medication_id]`;
        newRow.querySelector('select[name^="dosages"]').name = `medications[${rowCount}][taken_dosage]`;
        newRow.querySelector('input[type="checkbox"]').name = `medications[${rowCount}][is_completed]`;

        // 「この行を削除」ボタンにイベントリスナーを追加
        const removeButton = newRow.querySelector('.remove-row-btn');
        removeButton.addEventListener('click', () => {
            container.removeChild(removeButton.closest('.medication-row'));
            updateRowIndexes();
    });

    //完了buttonにイベントリスナーを追加
    const isCompletedCheckbox = newRow.querySelector('input[type="checkbox"]');
    isCompletedCheckbox.addEventListener('change',()=>{
        //理由を入力するコンテナを取得
        const reasonContainer = newRow.querySelector('.reason-container');

        if(!isCompletedCheckbox.checekd){
            //チェックされていなければ、理由の入力欄を表示
            const reasonField = document.getElementById('reason-templete').content.cloneNode(true);
            reasonContainer.appendChild(reasonField);
        }else{
            reasonContainer.innerHTML = '';
        }
    });

     // DOMに追加
    container.appendChild(newRow);

    }
// 初期行を追加
addRow();

// 「行を追加」ボタンにイベントリスナーを設定
addButton.addEventListener('click', addRow);

