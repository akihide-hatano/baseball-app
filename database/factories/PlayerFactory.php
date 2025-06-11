<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\Team; // Teamモデルを使用
use Illuminate\Database\Eloquent\Factories\Factory; // ★重要: ここは Factory を継承する！

class PlayerFactory extends Factory // ★クラス名が PlayerFactory であることを確認！★
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Player::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Fakerインスタンスを日本語ロケールで取得
        $faker = \Faker\Factory::create('ja_JP');

        // 存在するチームIDをランダムに取得
        $teamIds = Team::pluck('id')->toArray();
        $teamId = $faker->randomElement($teamIds); // PlayerSeederで上書きされるので、ここでは大雑把でOK

        // 身長と体重のランダム生成
        $height = $faker->numberBetween(165, 195);
        $weight = $faker->numberBetween(65, 100);

        return [
            'team_id' => $teamId,
            'name' => $faker->name('male'),
            // 'jersey_number' の生成は PlayerSeeder で制御するので、ここではコメントアウトか削除
            // 'jersey_number' => $faker->numberBetween(0, 99),
            'date_of_birth' => $faker->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
            'height' => $height,
            'weight' => $weight,
            'specialty' => $faker->randomElement(['速球派', '変化球派', '巧打者', '強打者', '俊足', '守備職人', 'オールラウンダー']),
            // descriptionも PlayerSeederで上書きするので、ここでは元に戻しておくか適当な内容でOK
            'description' => $faker->text(50),
            'hometown' => $faker->prefecture,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}