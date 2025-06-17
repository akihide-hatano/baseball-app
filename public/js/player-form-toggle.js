document.addEventListener('DOMContentLoaded', function () {

    const roleSelect = document.getElementById('role');
    const battingFields = document.getElementById('batting-stats-fields');
    const pitchingFields = document.getElementById('pitching-stats-fields');


    // 選手関連の要素が存在する場合のみ、toggleStatsFields 関数を定義・実行
    if (roleSelect && battingFields && pitchingFields) { // ★修正点: 全ての要素が存在するか確認
        function toggleStatsFields() {
            if (roleSelect.value === '野手') {
                battingFields.classList.remove('hidden');
                pitchingFields.classList.add('hidden');
                // 野手選択時、投手関連のフィールドを無効にする（フォーム送信から除外）
                pitchingFields.querySelectorAll('input, select, textarea').forEach(field => {
                    field.setAttribute('disabled', 'disabled');
                });
                // 野手関連のフィールドを有効にする
                battingFields.querySelectorAll('input, select, textarea').forEach(field => {
                    field.removeAttribute('disabled');
                });
            } else if (roleSelect.value === '投手') {
                pitchingFields.classList.remove('hidden');
                battingFields.classList.add('hidden');
                // 投手選択時、野手関連のフィールドを無効にする
                battingFields.querySelectorAll('input, select, textarea').forEach(field => {
                    field.setAttribute('disabled', 'disabled');
                });
                // 投手関連のフィールドを有効にする
                pitchingFields.querySelectorAll('input, select, textarea').forEach(field => {
                    field.removeAttribute('disabled');
                });
            } else { // 役割が未選択の場合など
                battingFields.classList.add('hidden');
                pitchingFields.classList.add('hidden');
                // どちらでもない場合、全て無効にする
                battingFields.querySelectorAll('input, select, textarea').forEach(field => {
                    field.setAttribute('disabled', 'disabled');
                });
                pitchingFields.querySelectorAll('input, select, textarea').forEach(field => {
                    field.setAttribute('disabled', 'disabled');
                });
            }
        }
        // 初期表示
        toggleStatsFields();
        // 役割選択が変更されたときに切り替え
        roleSelect.addEventListener('change', toggleStatsFields); // ここは既にroleSelectが存在する保証があるのでifは不要
    }
});