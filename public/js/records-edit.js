// 必要なHTML要素を取得
const addButton = document.getElementById('add-medication-row');
const container = document.getElementById('medication-container');
const template = document.getElementById('medication-template');
const reasonTpl = document.getElementById('reason-template');

/**
 * すべての行のインデックスを更新
 */
    function updateRowIndexes() {
      const rows = container.querySelectorAll('.medication-row');
      rows.forEach((row, i) => {
        row.querySelectorAll('[data-name]').forEach((el) => {
          const key = el.dataset.name;
          el.name = `medications[${i}][${key}]`;
        });
        const reasonSel = row.querySelector('select[data-name="reason_not_taken"]');
        if (reasonSel) {
          reasonSel.name = `medications[${i}][reason_not_taken]`;
        }
      });
    }

/**
 * 新しい薬の行を追加
 * @param {Object} [data] - 既存のデータ（編集時のみ）
 */
function addRow(data = {}) {
  const frag = template.content.cloneNode(true);

  // DOMに追加
  container.appendChild(frag);
  // 追加された“実体の行要素”を取得
  const row = container.lastElementChild;

// 既存データがあれば値を設定
if (data.medication_id) {
  // `data-name`で要素を取得
  row.querySelector('select[data-name="medication_id"]').value = data.medication_id;
}
if (data.taken_dosage) {
  // `data-name`で要素を取得
  row.querySelector('select[data-name="taken_dosage"]').value = data.taken_dosage;
}

  // `is_completed`のチェックと`reason_not_taken`の表示
  const isCompletedCheckbox = row.querySelector('input[data-name="is_completed"]');
  if (data.is_completed) {
    isCompletedCheckbox.checked = true;
  } else if (data.reason_not_taken) {
    const reasonContainer = row.querySelector('.reason-container');
    const reasonFrag = reasonTpl.content.cloneNode(true);
    const reasonSelect = reasonFrag.querySelector('select');
    reasonContainer.appendChild(reasonFrag);
    reasonSelect.value = data.reason_not_taken;
  }

  // 「この行を削除」
  row.querySelector('.remove-row-btn')?.addEventListener('click', () => {
    row.remove();
    updateRowIndexes();
  });

  // 完了チェックのトグルで理由フォームを表示/非表示
  isCompletedCheckbox.addEventListener('change', (e) => {
    const reasonContainer = row.querySelector('.reason-container');
    if (!e.target.checked) {
      if (!row.querySelector('select[data-name="reason_not_taken"]')) {
        const reasonFrag = reasonTpl.content.cloneNode(true);
        reasonContainer.appendChild(reasonFrag);
      }
    } else {
      reasonContainer.innerHTML = '';
    }
    updateRowIndexes();
  });

  // 追加直後の初期採番
  updateRowIndexes();
}


// 既存のデータがあれば、それに基づいて行を生成
if (existingMedications && existingMedications.length > 0) {
    existingMedications.forEach(med => {
        const medicationData = {
            medication_id: med.medication_id,
            taken_dosage: med.taken_dosage,
            is_completed: med.is_completed,
            reason_not_taken: med.reason_not_taken
        };
        addRow(medicationData);
    });
} else {
    // 既存データがなければ空の行を追加
    addRow();
}

// 「行を追加」ボタンにイベントリスナーを設定
addButton.addEventListener('click', () => addRow());