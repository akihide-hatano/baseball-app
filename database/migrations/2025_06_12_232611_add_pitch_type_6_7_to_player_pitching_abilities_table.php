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
        Schema::table('player_pitching_abilities', function (Blueprint $table) {
            // pitch_type_5 の後に pitch_type_6 と pitch_type_7 を追加
            $table->string('pitch_type_6')->nullable()->after('pitch_type_5');
            $table->string('pitch_type_7')->nullable()->after('pitch_type_6');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_pitching_abilities', function (Blueprint $table) {
            // down メソッドでは、追加したカラムを削除するように記述
            $table->dropColumn(['pitch_type_6', 'pitch_type_7']);
        });
    }
};