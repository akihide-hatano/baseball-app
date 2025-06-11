<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * playersテーブルにroleカラムを追加します。
     */
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('role')->after('jersey_number')->default('野手'); // '投手', 'レギュラー野手', '控え野手'などを保存
        });
    }

    /**
     * Reverse the migrations.
     * playersテーブルからroleカラムを削除します。
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};