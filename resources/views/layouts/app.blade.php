<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Barokah Toserba</title>
</head>
<body class="bg-gray-950 text-white flex min-h-screen font-sans">
    
    <aside class="w-64 bg-gray-900 border-r border-gray-800 p-6 fixed h-full flex flex-col justify-between z-10">
        
        <div>
            <h1 class="text-2xl font-bold text-cyan-400 mb-10 tracking-tighter italic">BAROKAH POS</h1>
            
            <nav class="space-y-2">
                <a href="/" class="flex items-center p-3 rounded-xl transition {{ request()->is('/') ? 'bg-cyan-600 text-white' : 'hover:bg-gray-800 text-gray-400' }}">
                    <span class="mr-3">📦</span> Inventaris
                </a>
                <a href="/laporan" class="flex items-center p-3 rounded-xl transition {{ request()->is('laporan') ? 'bg-cyan-600 text-white' : 'hover:bg-gray-800 text-gray-400' }}">
                    <span class="mr-3">📊</span> Laporan
                </a>
                <a href="/pos" class="flex items-center p-3 rounded-xl transition {{ request()->is('pos') ? 'bg-cyan-600 text-white' : 'hover:bg-gray-800 text-gray-400' }}">
                    <span class="mr-3">🛒</span> Pos
                </a>
            </nav>
        </div>

        <div class="border-t border-gray-800 pt-4 mb-2">
            <form action="{{ route('logout') }}" method="POST">
                @csrf <button type="submit" 
                    class="flex items-center w-full p-3 rounded-xl text-gray-400 hover:bg-red-950/40 hover:text-red-400 transition font-medium text-left focus:outline-none">
                    <i class="fas fa-sign-out-alt mr-3 mt-0.5"></i> Logout
                </button>
            </form>
        </div>

    </aside>

    <main class="flex-1 ml-64 p-8">
        @yield('content')
    </main>

</body>
</html>