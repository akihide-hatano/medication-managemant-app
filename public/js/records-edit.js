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
      element.name = `medications[${j}][${key}]`
    }
  }
}