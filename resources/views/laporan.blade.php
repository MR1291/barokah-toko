@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold text-cyan-400 italic">Laporan Penjualan</h1>
    <div class="bg-gray-900 rounded-xl p-1 border border-gray-800 text-xs font-bold uppercase">
        <a href="/laporan?view=daily" class="px-6 py-2 rounded-lg inline-block {{ $view == 'daily' ? 'bg-cyan-600' : '' }}">Harian</a>
        <a href="/laporan?view=monthly" class="px-6 py-2 rounded-lg inline-block {{ $view == 'monthly' ? 'bg-cyan-600' : '' }}">Bulanan</a>
    </div>
</div>

<div class="bg-gray-900 border border-gray-800 p-8 rounded-3xl shadow-2xl">
    <canvas id="chartPenjualan" height="110"></canvas>
</div>

<script>
    const ctx = document.getElementById('chartPenjualan').getContext('2d');
    const db = @json($data);
    const labels = db.map(i => {
        if("{{ $view }}" == 'monthly') {
            return ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][parseInt(i.label)-1];
        }
        return i.label;
    });
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Omzet (Rp)',
                data: db.map(i => i.total),
                borderColor: '#22d3ee',
                backgroundColor: 'rgba(34, 211, 238, 0.1)',
                fill: true, tension: 0.4, borderWidth: 4, pointRadius: 5
            }]
        },
        options: {
            scales: { y: { grid: { color: '#1e293b' }, ticks: { color: '#94a3b8' } }, x: { grid: { display: false }, ticks: { color: '#94a3b8' } } }
        }
    });
</script>
@endsection