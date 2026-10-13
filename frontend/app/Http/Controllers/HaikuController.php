<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AjiAiService;

class HaikuController extends Controller
{
    // メイン画面の表示
    public function index()
    {
        $theme = "秋の夕暮れ"; // 今は固定。ランダム生成にしても面白いです
        return view('haiku.index', compact('theme'));
    }

    // 俳句の判定処理
    public function battle(Request $request, AjiAiService $aiService)
    {
        // バリデーション（五・七・五の入力チェック）
        $request->validate([
            'kami'  => 'required|string|max:15',
            'naka'  => 'required|string|max:15',
            'shimo' => 'required|string|max:15',
            'theme' => 'required|string',
        ]);

        $userHaiku = "{$request->kami} {$request->naka} {$request->shimo}";
        
        // Serviceクラスを使ってAzure OpenAIを呼び出す
        $result = $aiService->evaluateHaiku($request->theme, $userHaiku);

        if (!$result) {
            return back()->with('error', '安治が考え込んでしまいました。もう一度お試しください。');
        }

        // 結果を画面に返す
        return view('haiku.index', [
            'theme' => $request->theme,
            'userHaiku' => $userHaiku,
            'result' => $result
        ]);
    }
}