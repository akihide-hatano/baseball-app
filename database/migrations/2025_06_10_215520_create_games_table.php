<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->date('game_date'); // 試合日
            $table->foreignId('home_team_id')->constrained('teams')->onDelete('cascade'); // ホームチームID
            $table->foreignId('away_team_id')->constrained('teams')->onDelete('cascade'); // アウェイチームID
            $table->integer('home_team_score')->default(0); // ホームチーム得点
            $table->integer('away_team_score')->default(0); // アウェイチーム得点
            $table->foreignId('winning_team_id')->nullable()->constrained('teams')->onDelete('set null'); // 勝利チームID (引き分けの場合はNULL)
            $table->foreignId('losing_team_id')->nullable()->constrained('teams')->onDelete('set null'); // 敗戦チームID (引き分けの場合はNULL)
            $table->boolean('draw')->default(false); // 引き分けフラグ (true:引き分け, false:勝敗あり)
            $table->string('venue')->nullable(); // 開催球場名
            $table->string('game_status')->default('完了'); // 試合の状態 (例: '完了', '中止', '延期', '進行中')
            $table->text('description')->nullable(); // 試合の概要や特記事項を追加
            $table->timestamps();

            // 同じ日に同じ対戦カード（ホームとアウェイが逆でもOK）が複数登録されないように制約を検討することも可能ですが、
            // 現実にはダブルヘッダーなどもあり得るため、厳密なユニーク制約は置かないでおきます。
            // 必要であれば複合ユニークキーの追加を検討します。
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};