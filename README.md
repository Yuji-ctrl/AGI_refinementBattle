# Aji（安治）— 渋おじAIと俳句で勝負

Ajiは、ユーザーが詠んだ俳句をAIキャラクター「安治（Aji）」が講評し、AIの句と対決できるWebアプリです。  
Azure AI Foundry上のOpenAI互換APIをLaravelから呼び出し、お題に対するユーザーの句を評価します。

## 概要

- ユーザーがお題に合わせて「上五・中七・下五」の俳句を入力
- Azure AI Foundry上のLLMがユーザーの句を講評
- AIキャラクター「安治」の句と勝敗を比較して表示
- LaravelのMVC構成、ServiceクラスによるAI連携の分離を学習することも目的

## 使用技術

- PHP 8.5
- Laravel 13
- Blade
- Tailwind CSS（CDN）
- SQLite（セッション等のローカル開発用）
- Azure AI Foundry / OpenAI互換Chat Completions API

## 必要要件

- PHP 8.5以上
- Composer
- Azure AI Foundryのプロジェクトとモデルデプロイメント
- Azure OpenAI / AI Foundry用のAPIキー

## セットアップ

```bash
git clone <このリポジトリのURL>
cd <リポジトリ名>

composer install

cp .env.example .env
php artisan key:generate
```

## 環境変数

`.env` に以下を設定します。

```env
AZURE_OPENAI_ENDPOINT="https://<your-resource>.services.ai.azure.com/openai/v1"
AZURE_OPENAI_DEPLOYMENT="gpt-5.4-nano"
AZURE_OPENAI_API_KEY="your-api-key"
```

- `AZURE_OPENAI_ENDPOINT` は、Azure AI FoundryのOpenAI互換エンドポイント（`/openai/v1` まで）を指定します。
- `AZURE_OPENAI_DEPLOYMENT` には、Foundryでデプロイしたモデル名を指定します。
- OpenAI互換APIのため、従来のAzure OpenAI形式のような `api-version` は不要です。

## 起動方法

```bash
php artisan config:clear
php artisan serve
```

ブラウザで以下にアクセスします。

```text
http://127.0.0.1:8000
```

## 使い方

1. トップ画面に表示されるお題を確認します。
2. 「上五」「中七」「下五」に分けて俳句を入力します。
3. 「安治に挑む」を押します。
4. 安治の講評、安治の句、勝敗結果が表示されます。

## ディレクトリ構成

```text
app/
├── Http/
│   └── Controllers/
│       └── HaikuController.php   # 画面表示とフォーム送信
└── Services/
    └── AjiAiService.php          # Azure AI FoundryとのAPI連携

resources/
└── views/
    └── haiku/
        └── index.blade.php       # お題・入力・結果画面

routes/
└── web.php                       # ルーティング
```

## API連携の仕組み

`AjiAiService` がAzure AI FoundryのOpenAI互換エンドポイントへ以下のようなリクエストを送信します。

```text
POST {AZURE_OPENAI_ENDPOINT}/chat/completions
Authorization: Bearer {API_KEY}
```

リクエストボディには、安治の人格を定義したシステムプロンプト、お題、ユーザーの俳句を含めます。  
レスポンスはJSON形式で受け取り、安治の句・講評・勝者を画面に表示します。

## トラブルシューティング

### `cURL error 60: SSL certificate problem`

PHPがHTTPS通信時のルート証明書を参照できていない可能性があります。

1. [curlの公式サイト](https://curl.se/docs/caextract.html)から `cacert.pem` をダウンロードします。
2. 例として `C:/php_ssl/cacert.pem` に配置します。
3. 使用中の `php.ini` に以下を追記します。

```ini
curl.cainfo = "C:/php_ssl/cacert.pem"
openssl.cafile = "C:/php_ssl/cacert.pem"
```

4. `php artisan serve` を再起動します。

### `API version not supported`

Azure AI FoundryのOpenAI互換エンドポイントを利用している場合、従来のAzure OpenAI用 `api-version` は不要です。  
`.env` のエンドポイントを `/openai/v1` までにし、`AjiAiService` 側では `{endpoint}/chat/completions` を呼び出してください。

### 「安治が考え込んでしまいました」と表示される

AI APIの呼び出しが失敗しています。以下を確認してください。

```bash
php artisan config:clear
```

そのうえで、`storage/logs/laravel.log` を確認し、HTTPステータスコードやAzure側のエラーメッセージを確認してください。

## 今後の拡張案

- お題のランダム生成
- 俳句の履歴保存
- 連勝数・味わいレベルの記録
- 画像・音声入力からの句生成
- ユーザー認証とランキング機能

## ライセンス

未定
