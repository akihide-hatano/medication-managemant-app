// 必要なHTML要素を取得
const addButton = document.getElementById('add-medication-row');
const container = document.getElementById('medication-container');
const template = document.getElementById('medication-template');
const reasonTpl = document.getElementById('reason-template'); // 新しいテンプレート参照

/**
 * すべての行のインデックスを更新する関数
 */
    function updateRowIndexes(){
        const rows = container.children;
        for(let i = 0; i < rows.length; i++){
            const row = rows[i];
            row.querySelector('select[name^="medications"]').name = `medications[${i}][medication_id]`;
            row.querySelector('select[name^="medications"][name*="taken_dosage"]').name = `medications[${i}][taken_dosage]`; // ★ここを修正★
            row.querySelector('input[type="checkbox"]').name = `medications[${i}][is_completed]`;
        }
    }

/**
 * 新しい薬の行を追加する関数
 */
    function addRow() {
        const newRow = template.content.cloneNode(true);
        const rowCount = container.children.length;

        // name属性のインデックスを更新（DOMに追加する前に実行）
        newRow.querySelector('select[name^="medications"]').name = `medications[${rowCount}][medication_id]`;
        newRow.querySelector('select[name*="taken_dosage"]').name = `medications[${rowCount}][taken_dosage]`; // ★ここを修正★
        newRow.querySelector('input[type="checkbox"]').name = `medications[${rowCount}][is_completed]`;

        // DOMに追加
        container.appendChild(newRow);

        // 追加されたばかりの行（実体）を改めて取得
        const addedRow = container.lastElementChild;

        // 「この行を削除」ボタンにイベントリスナーを追加
        const removeButton = addedRow.querySelector('.remove-row-btn');
        removeButton.addEventListener('click', () => {
            addedRow.remove();
            updateRowIndexes();
        });

        // 完了ボタンにイベントリスナーを追加
        const isCompletedCheckbox = addedRow.querySelector('input[type="checkbox"]');
        isCompletedCheckbox.addEventListener('change', () => {
            const reasonContainer = addedRow.querySelector('.reason-container'); // DOM上の実体を参照
            console.log(reasonContainer);

            if (!isCompletedCheckbox.checked) {
                const reasonField = reasonTpl.content.cloneNode(true);
                // 新しいドロップダウンのname属性を更新
                reasonField.querySelector('select').name = `medications[${rowCount}][reason_not_taken]`;
                reasonContainer.appendChild(reasonField);
            } else {
                reasonContainer.innerHTML = '';
            }
        });

        // 追加直後の初期インデックスを更新
        updateRowIndexes();
    }

// 初期行を追加
  addRow();

// 「行を追加」ボタンにイベントリスナーを設定
    addButton.addEventListener('click', addRow);