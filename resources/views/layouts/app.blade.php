<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Barokah Toserba</title>
</head>
<body class="bg-gray-950 text-white flex min-h-screen font-sans">
    <aside class="w-64 bg-gray-900 border-r border-gray-800 p-6 fixed h-full flex flex-col">
        <h1 class="text-2xl font-bold text-cyan-400 mb-10 tracking-tighter italic">BAROKAH POS</h1>
        <nav class="space-y-2 flex-1">
            <a href="/" class="flex items-center p-3 rounded-xl transition {{ request()->is('/') ? 'bg-cyan-600 text-white' : 'hover:bg-gray-800 text-gray-400' }}">
                <span class="mr-3">📦</span> Inventaris
            </a>
            <a href="/laporan" class="flex items-center p-3 rounded-xl transition {{ request()->is('laporan') ? 'bg-cyan-600 text-white' : 'hover:bg-gray-800 text-gray-400' }}">
                <span class="mr-3">📊</span> Laporan
            </a>
        </nav>
    </aside>
    <main class="flex-1 ml-64 p-8">
        @yield('content')
    </main>
</body>
</html>