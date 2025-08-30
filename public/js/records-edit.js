//要素を取得
const addButton = document.getElementById('add-medication-row');
const container = document.getElementById('medication-container');
const template = document.getElementById('medication-template');
const reasonTpl = document.getElementById('reason-template');

/**
 * すべての行のインデックスを更新する関数
 */

  function updateRowIndexes(){
    const rows = container.querySelectorAll('.medication-row');

    for( let i =0; i < rows.length; i++ ){
      const row = rows[i];
      const elements = row.querySelectorAll('[data-name]');

      for( let j = 0; j < elements.length; j++ ){
        const element = elements[j];
        const key = element.dataset.name;
        element.name = `medications[${i}][${key}]`
      }
    }
  }

/**
 * 新しい薬の行を追加する関数
 * @param {Object} [data] - 既存のデータ（編集時のみ）
 */
    function addRow(data=[{}]){
      const frag = template.content.cloneNode(true);
      const row = frag.firstElementChild;

      //内服薬のdataがあれば値を設定
      if(data.medication_id){
        const medicationSelect = row.querySelector('[data-name="medication_id"]');
        medicationSelect.value = data.medication_id;
      }

      //内服量のdataを設定
      if(data.taken_dosage){
        const dosageSelect = row.querySelector('[data-name="taken_dosage"]');
        dosageSelect.value = data.taken_dosage;
      }

      //`is_completed`のチェックと`reason_not_taken`の表示
      const isCompletedCheckbox = row.querySelector('[data-name="is_completed"]');
      if(data.is_completed){
        isCompletedCheckbox.checked = true;
      } else if(data.reason_not_taken){
        const reasonContainer = row.querySelector('.reason-container');
        const reasonFrag= reasonTpl.content.cloneNode(true);
        const reasonSelect = reasonFrag.querySelector('[data-name="reason_not_taken"]');
        reasonContainer.appendChild(reasonFrag);
        reasonSelect.value = data.reason_not_taken;
      }

      //この行を削除のボタンにイベントリスナーを追加
      const removeButton = row.querySelector('.remove-row-btn');
      if(removeButton){
        removeButton.addEventListener('click',()=>{
          row.remove();
          updateRowIndexes();
        });
      }

      //完了チェックのトグルで理由フォームを表示/非表示
      isCompletedCheckbox.addEventListener('change',(e)=>{
        const reasonContainer = row.querySelector('.reason-container');
        if(!e.target.checked){
          if(!row.querySelector('[data-name="reason_not_taken"]')){
              reasonContainer.appendChild(reasonTpl.contentEditable.cloneNode(true));
          }else{
              reasonContainer.innerHTML = '';
          }
          updateRowIndexes();
        }
      });

      //DOMに追加
      container.appendChild(row);
      //追加直後のインデックスを更新
      updateRowIndexes();
    }

    //既存データがあれば、for文でそれそれに行を生成
    if(existingMedications && existingMedications.length > 0 ){
      //データの数を繰り返す
      for( let i=0; i < existingMedications.length; i++){
          const medicationData = existingMedications[i];
          addRow(medicationData);
        }
      }else{
      //データがなければ、、空の行を1つ追加
      addRow();
      }
    // 「行を追加」ボタンにイベントリスナーを設定
    addButton.addEventListener('click', function() {
      addRow();
    });