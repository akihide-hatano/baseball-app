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
        Schema::create('player_pitching_abilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->integer('year'); // 能力の年度 (例: 2024年の能力)

            // 投球能力値
            $table->integer('pitch_control')->default(0); // 制球力
            $table->integer('pitch_stamina')->default(0); // スタミナ
            $table->decimal('average_velocity', 4, 1)->default(0.0); // 平均球速 (例: 145.5)

            // 変化球の種類とレベル (例: 'カーブ:4', 'フォーク:3' など)
            $table->string('pitch_type_1')->nullable(); // ★この行と以下4行を追加（またはコメントアウト解除）★
            $table->string('pitch_type_2')->nullable();
            $table->string('pitch_type_3')->nullable();
            $table->string('pitch_type_4')->nullable();
            $table->string('pitch_type_5')->nullable();

            $table->integer('overall_rank')->nullable(); // 総合能力ランク (例: 70A, 80Sなど表示用)
            $table->text('special_skills')->nullable(); // 特殊能力（カンマ区切り文字列またはJSONなど）

            $table->timestamps();

            $table->unique(['player_id', 'year']); // 同じ選手の同じ年度の能力は1つ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_pitching_abilities');
    }
};