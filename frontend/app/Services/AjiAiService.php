<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AjiAiService
{
    public function evaluateHaiku(string $theme, string $userHaiku): ?array
    {
        // .envから値を取得（末尾の / を念のため削除）
        $endpoint = rtrim(env('AZURE_OPENAI_ENDPOINT'), '/');
        $deployment = env('AZURE_OPENAI_DEPLOYMENT');
        $apiKey = env('AZURE_OPENAI_API_KEY');

        // OpenAI v1 互換のChat Completions URL
        $url = "{$endpoint}/chat/completions";

        $systemPrompt = <<<EOT
あなたは「安治（AGI）」という60代の渋くて味のあるおじさんAIです。
ユーザーと俳句で勝負をします。お題に沿ったあなたの俳句と、ユーザーの俳句への講評、そして勝敗を判定してください。
口調は少し偉そうですが、風流を解する文化人です。
（例：「ふむ、悪くないが若すぎるな」「なかなか味のある句を詠むではないか」）
必ず以下のJSONフォーマットのみで出力してください。
{
    "aji_haiku": "安治の詠んだ俳句",
    "evaluation": "安治による渋い講評",
    "winner": "user または aji"
}
EOT;

        // FoundryのServerlessエンドポイントは通常 Authorization ヘッダーを使用します
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type'  => 'application/json',
        ])->post($url, [
            'model'    => $deployment, // URLではなく、ここでモデル名を指定します！
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "お題は「{$theme}」です。私の句は「{$userHaiku}」です。いざ勝負！"],
            ],
            'temperature'     => 0.7,
            'response_format' => ['type' => 'json_object'],
        ]);

        if ($response->successful()) {
            $content = $response->json('choices.0.message.content');
            return json_decode($content, true);
        }

        // エラー時のデバッグ用ログ出力
        Log::error('AI API Error: ' . $response->status() . ' - ' . $response->body());
        return null;
    }
}