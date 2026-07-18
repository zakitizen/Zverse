<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pewarta Login — Zverse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4">
<div class="w-full max-w-sm">
    <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 mb-2">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 via-orange-500 to-purple-600 shadow-lg shadow-orange-500/20">
                @php $zvLogo = 'logozverse.png'; @endphp
                <img src="{{ asset($zvLogo) }}?v={{ filemtime(public_path($zvLogo)) }}" alt="Zverse" class="h-6 w-6 object-contain" />
            </div>
            <div class="leading-none text-left">
                <span class="block text-white text-2xl font-black tracking-tight">Zverse</span>
                <span class="text-[10px] font-semibold uppercase tracking-[0.25em] text-slate-500">Pewarta</span>
            </div>
        </a>
        <p class="text-gray-500 text-sm">Portal Pewarta</p>
    </div>
    <div class="bg-gray-900 rounded-2xl border border-gray-800 p-8">
        <h2 class="text-white text-xl font-bold mb-6 text-center">Masuk sebagai Pewarta</h2>
        @if($errors->any())
        <div class="bg-red-900/50 border border-red-700 text-red-300 text-sm px-4 py-3 rounded-xl mb-5">{{ $errors->first() }}</div>
        @endif
        <form action="{{ route('pewarta.login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-400 text-sm font-semibold mb-1.5">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required autofocus
                       class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 placeholder-gray-600"
                       placeholder="username pewarta">
            </div>
            <div>
                <label class="block text-gray-400 text-sm font-semibold mb-1.5">Password</label>
                <input type="password" name="password" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 placeholder-gray-600"
                       placeholder="••••••••">
            </div>
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-xl transition-colors">Masuk</button>
        </form>
        <p class="text-gray-600 text-xs text-center mt-4">Demo: <span class="text-gray-400 font-mono">rizky / pewarta123</span></p>
    </div>
    <div class="mt-4 text-center"><a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-400 text-xs">Kembali ke Zverse</a></div>
</div>
</body>
</html>
