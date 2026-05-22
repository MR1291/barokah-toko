<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login - Barokah Toserba</title>
</head>
<body class="bg-gray-950 text-white flex min-h-screen items-center justify-center font-sans p-4">

    <div class="w-full max-w-md bg-gray-900 border border-gray-800 p-8 rounded-2xl shadow-2xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black italic text-cyan-400 tracking-tighter">BAROKAH POS</h1>
            <p class="text-gray-500 text-sm mt-2">Silakan login untuk masuk ke sistem kasir</p>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-emerald-950/50 border border-emerald-500/30 text-emerald-400 p-3 rounded-xl text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="username" class="block text-sm font-medium text-gray-400 mb-2">Username</label>
                <input type="text" name="username" id="username" 
                    value="{{ old('username') }}"
                    class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition" 
                    placeholder="Masukkan username kamu" required autofocus>
                
                @error('username')
                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-400 mb-2">Password</label>
                <input type="password" name="password" id="password" 
                    class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition" 
                    placeholder="••••••••" required>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center space-x-2 text-gray-400 cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="rounded border-gray-800 bg-gray-950 text-cyan-500 focus:ring-0 focus:ring-offset-0">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" 
                class="w-full bg-cyan-600 hover:bg-cyan-500 text-white font-semibold py-3 px-4 rounded-xl transition shadow-lg shadow-cyan-950/50 focus:outline-none active:scale-[0.98]">
                Masuk Sistem
            </button>
        </form>
    </div>

</body>
</html>