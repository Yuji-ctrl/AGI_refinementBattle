<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>人生の味判定アプリ - 安治</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-200 text-stone-800 font-serif">
    <div class="max-w-md mx-auto bg-white min-h-screen shadow-lg relative">
        <!-- ヘッダー -->
        <header class="bg-stone-800 text-stone-100 p-4 text-center">
            <h1 class="text-xl font-bold">風流の庵 - 渋おじAI 安治</h1>
        </header>

        <main class="p-6">
            <!-- 安治のセリフエリア -->
            <div class="mb-8 flex gap-4 items-start">
                <div class="w-16 h-16 bg-stone-300 rounded-full flex items-center justify-center shrink-0">
                    <span class="text-sm">安治画像</span>
                </div>
                <div class="bg-stone-100 p-4 rounded-lg rounded-tl-none border border-stone-300">
                    <p>今日のお題は<strong>「{{ $theme }}」</strong>だ。<br>お手前、私を唸らせる句が詠めるかな？</p>
                </div>
            </div>

            <!-- エラーメッセージ -->
            @if (session('error'))
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
            @endif

            <!-- 入力フォーム -->
            <form action="{{ route('haiku.battle') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="theme" value="{{ $theme }}">
                
                <div class="flex justify-between gap-2">
                    <input type="text" name="kami" placeholder="上五" required class="w-1/3 border-b-2 border-stone-400 p-2 focus:outline-none focus:border-stone-800 text-center bg-transparent">
                    <input type="text" name="naka" placeholder="中七" required class="w-1/3 border-b-2 border-stone-400 p-2 focus:outline-none focus:border-stone-800 text-center bg-transparent">
                    <input type="text" name="shimo" placeholder="下五" required class="w-1/3 border-b-2 border-stone-400 p-2 focus:outline-none focus:border-stone-800 text-center bg-transparent">
                </div>

                <button type="submit" class="w-full bg-stone-800 text-white py-3 rounded hover:bg-stone-700 transition">
                    安治に挑む
                </button>
            </form>

            <!-- 判定結果（対戦後のみ表示） -->
            @isset($result)
                <div class="mt-8 border-t-2 border-dashed border-stone-300 pt-6 mt-6 animate-fade-in">
                    <h2 class="text-xl font-bold text-center mb-4 border-b pb-2">立ち合い結果</h2>
                    
                    <div class="flex justify-between mb-4">
                        <div class="w-5/12 text-center p-2 bg-blue-50 rounded">
                            <span class="text-xs text-blue-600 block mb-1">あなたの句</span>
                            <p class="font-bold">{{ $userHaiku }}</p>
                        </div>
                        <div class="w-2/12 flex items-center justify-center font-bold text-stone-400">VS</div>
                        <div class="w-5/12 text-center p-2 bg-red-50 rounded">
                            <span class="text-xs text-red-600 block mb-1">安治の句</span>
                            <p class="font-bold">{{ $result['aji_haiku'] }}</p>
                        </div>
                    </div>

                    <div class="bg-stone-100 p-4 rounded mb-4">
                        <span class="text-xs font-bold text-stone-500 block mb-2">安治の講評</span>
                        <p class="text-sm">{{ $result['evaluation'] }}</p>
                    </div>

                    <div class="text-center text-2xl font-bold p-4 bg-stone-800 text-yellow-400 rounded">
                        勝者：{{ $result['winner'] === 'user' ? 'あなた' : '安治' }}
                    </div>
                    
                    <a href="{{ route('haiku.index') }}" class="block text-center mt-4 text-sm text-stone-500 underline">別のお題で詠む</a>
                </div>
            @endisset
        </main>
    </div>
</body>
</html>