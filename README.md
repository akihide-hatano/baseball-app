# 私のプロ野球データ管理システム

プロ野球のデータを保存できるようなサイトです。<br>
プロ野球選手の試合結果や選手各々の実績をグラフを使って可視化しました。

- **Home画面**<br>
  各セクションから既存データの一覧表示や、新規データの登録ページへ直接アクセス可能です。
<img src="/public/image/home.png" alt="home" style="width: 80%;">

- **Player画面**<br>
  選手の一覧を確認
<img src="/public/image/players.png" alt="home" style="width: 80%;">

- **Teams画面**<br>
  チーム一覧とチーム新規作成とチームの検索
<img src="/public/image/teams.png" alt="home" style="width: 80%;">

- **Games画面**<br>
  試合一覧と試合の検索
<img src="/public/image/games.png" alt="home" style="width: 80%;">

# URL
http://the-view.work/<br>
ユーザー登録をしてメールアドレスとパスワードで登録してください。

# 使用技術
- **バックエンド**:
  - Laravel 12 (PHP 8.3+ または最新推奨バージョン)
  - Composer
- **データベース**: PostgreSQL
- **フロントエンド**:
  - Bladeテンプレート
  - Tailwind CSS
  - Chart.js
  - Vite (アセットバンドル)
- **認証**: Laravel Breeze
- **開発・デプロイ環境**:
  - Docker / Docker Compose (Laravel Sail)
  - Git / GitHub
- **テスト**: PHPUnit (Laravelのデフォルトテストフレームワーク)
- **パンくずリスト**: Diglactic/laravel-breadcrumbs パッケージ

# 機能一覧
- ユーザー認証機能（ログイン、新規登録、プロフィール編集）
- **選手情報管理**:
  - 選手の登録、編集、削除、一覧表示
  - 選手詳細ページでの能力（打撃・投手）および年度別成績の記録・管理
- **チーム情報管理**:
  - チームの登録、編集、削除、一覧表示
  - チーム詳細ページ
- **試合情報管理**:
  - 試合の登録、編集、削除、一覧表示
  - 試合詳細ページでのスコア、結果、選手成績の閲覧
  - 試合一覧のフィルタリング（チーム、月別）とページネーション
- **データ可視化**:
  - 各選手の能力や成績をグラフ（レーダーチャート、棒グラフなど）で視覚化
- パンくずリスト