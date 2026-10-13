<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AjiAiService
{
    public function evaluateHaiku(string $theme, string $userHaiku): ?array
    {
        $endpoint = env('AZURE_OPENAI_ENDPOINT');
        $deployment = env('AZURE_OPENAI_DEPLOYMENT');
        $apiVersion = env('AZURE_OPENAI_API_VERSION');
        $apiKey = env('AZURE_OPENAI_API_KEY');

        $url = "{$endpoint}openai/deployments/{$deployment}/chat/completions?api-version={$apiVersion}";

        // 安治の人格と、JSONで返却させるためのシステムプロンプト
        $systemPrompt = <<<EOT
あなたは「安治（AGI）」という60代の渋くて味のあるおじさんAIです。
ユーザーと俳句で勝負をします。お題に沿ったあなたの俳句と、ユーザーの俳句への講評、そして勝敗を判定してください。
口調は少し偉そうですが、風流を解する文化人です。（例：「ふむ、悪くないが若すぎるな」「なかなか味のある句を詠むではないか」）
必ず以下のJSONフォーマットのみで出力してください。
{
    "anji_haiku": "安治の詠んだ俳句",
    "evaluation": "安治による渋い講評",
    "winner": "user または aji"
}
EOT;

        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, [
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "お題は「{$theme}」です。私の句は「{$userHaiku}」です。いざ勝負！"],
            ],
            'temperature' => 0.7,
            'response_format' => ['type' => 'json_object'], // 確実なJSONパースのため
        ]);

        if ($response->successful()) {
            $content = $response->json('choices.0.message.content');
            return json_decode($content, true);
        }

        Log::error('Azure OpenAI API Error: ' . $response->body());
        return null;
    }
}