const playerForm = document.getElementById('playerCreationForm');
const gameEditForm = document.getElementById('gameEditForm');     // 試合編集フォーム用
const gameDeleteForm = document.getElementById('gameDeleteForm');

    console.log('playerForm element:', playerForm);
    console.log('gameEditForm element:', gameEditForm);
    console.log('gameDeleteForm element:', gameDeleteForm);


    // フォーム送信時の確認のダイアログ (選手フォーム用)
    if (playerForm) {
        playerForm.addEventListener('submit', (e) => {
            if (!confirm('選手情報を更新してもよろしいですか？')) { // メッセージを明確化
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