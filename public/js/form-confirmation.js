const playerForm = document.getElementById('playerCreationForm');
const playerDeleteForm = document.getElementById('playerDeleteForm');
const gameEditForm = document.getElementById('gameEditForm');     // 試合編集フォーム用
const gameDeleteForm = document.getElementById('gameDeleteForm');
const battingDeleteForm = document.getElementById('battingDeleteForm');


    // フォーム送信時の確認のダイアログ (選手フォーム用)
    if (playerForm) {
        playerForm.addEventListener('submit', (e) => {
            if (!confirm('選手情報を更新してもよろしいですか？')) { // メッセージを明確化
                e.preventDefault();
            }
        });
    }

    if (playerDeleteForm) {
        playerDeleteForm.addEventListener('submit', (e) => {
            if (!confirm('選手情報を削除してもよろしいですか？')) { // メッセージを明確化
                e.preventDefault();
            }
        });
    }

    // フォーム送信時の確認のダイアログ (試合編集フォーム用)
    if (gameEditForm) {
        gameEditForm.addEventListener('submit', (e) => {
            if (!confirm('本当にこの試合情報を更新しますか？')) {
                e.preventDefault();
            }
        });
    }

    if(gameDeleteForm){
            gameDeleteForm.addEventListener('submit',(e)=>{
                if(!confirm('本当にこの試合を削除してもいいですか')){
                    e.preventDefault();
                }
            });
        }

    if(battingDeleteForm){
            battingDeleteForm.addEventListener('submit',(e)=>{
                if(!confirm('本当にこの打撃能力データを削除してもいいですか')){
                    e.preventDefault();
                }
        });
}