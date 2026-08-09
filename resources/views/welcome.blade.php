<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Metrics Dashboard - {{ config('app.name', 'MultiFunction-APP') }}</title>
    
    <!-- Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-[#1e293b] text-slate-100 font-sans antialiased min-h-screen">

    <!-- Top Navigation Header -->
    <header class="bg-[#0f172a] border-b border-slate-800 sticky top-0 z-50 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-3 h-3 bg-emerald-500 rounded-full animate-ping"></div>
            <h1 class="text-xl font-bold tracking-wide uppercase text-slate-200">SERVER PERFORMANCE METRICS</h1>
            <span class="bg-slate-800 text-slate-400 text-xs px-2.5 py-1 rounded-md border border-slate-700">IP: {{ $metrics['server_ip'] }}</span>
        </div>
        
        <div class="flex items-center space-x-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-semibold transition">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 rounded-lg text-sm font-semibold transition">Log In</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-sm font-semibold transition">Register Account</a>
                    @endif
                @endauth
            @endif
        </div>
    </header>

    <!-- Main Dashboard Grid -->
    <main class="max-w-7xl mx-auto p-6 space-y-6">

        <!-- Top Overview Bar -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-[#0f172a] border border-slate-800 p-5 rounded-xl">
                <span class="text-xs font-semibold text-slate-400 uppercase">SYSTEM UPTIME</span>
                <p class="text-lg font-bold text-emerald-400 mt-1">{{ $metrics['uptime'] }}</p>
            </div>
            <div class="bg-[#0f172a] border border-slate-800 p-5 rounded-xl">
                <span class="text-xs font-semibold text-slate-400 uppercase">CPU TEMPERATURE</span>
                <p class="text-2xl font-bold text-amber-400 mt-1">{{ $metrics['cpu_temp'] }}°C</p>
            </div>
            <div class="bg-[#0f172a] border border-slate-800 p-5 rounded-xl">
                <span class="text-xs font-semibold text-slate-400 uppercase">RAM USAGE</span>
                <p class="text-2xl font-bold text-sky-400 mt-1">{{ $metrics['ram_used_gb'] }} GB <span class="text-xs text-slate-500">/ {{ $metrics['ram_total_gb'] }} GB</span></p>
            </div>
            <div class="bg-[#0f172a] border border-slate-800 p-5 rounded-xl">
                <span class="text-xs font-semibold text-slate-400 uppercase">STORAGE USAGE</span>
                <p class="text-2xl font-bold text-indigo-400 mt-1">{{ $metrics['disk_used_gb'] }} GB <span class="text-xs text-slate-500">/ {{ $metrics['disk_total_gb'] }} GB</span></p>
            </div>
        </div>

        <!-- Middle Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- 1. CPU Rate Doughnut / Gauge Chart -->
            <div class="bg-[#0f172a] border border-slate-800 p-6 rounded-xl flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-1">CPU LOAD RATE</h3>
                    <p class="text-xs text-slate-500">Real-time processor utilization percentage</p>
                </div>
                <div class="relative flex items-center justify-center my-6 h-48">
                    <canvas id="cpuChart"></canvas>
                    <div class="absolute text-center">
                        <span class="text-3xl font-extrabold text-slate-100">{{ $metrics['cpu_usage'] }}%</span>
                        <p class="text-xs text-slate-400">CPU Usage</p>
                    </div>
                </div>
                <div class="flex justify-between text-xs text-slate-400 border-t border-slate-800 pt-3">
                    <span>Core Status: <strong class="text-emerald-400">Optimal</strong></span>
                    <span>Load: <strong>{{ $metrics['cpu_usage'] }}%</strong></span>
                </div>
            </div>

            <!-- 2. Memory & Disk Distribution Chart -->
            <div class="bg-[#0f172a] border border-slate-800 p-6 rounded-xl">
                <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-1">STORAGE & MEMORY CAPACITY</h3>
                <p class="text-xs text-slate-500">Used vs Free distribution ratio</p>
                <div class="h-56 mt-4">
                    <canvas id="resourceChart"></canvas>
                </div>
            </div>

            <!-- 3. Top Services Status / Process Breakdown -->
            <div class="bg-[#0f172a] border border-slate-800 p-6 rounded-xl">
                <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-1">CORE SERVICES HEALTH</h3>
                <p class="text-xs text-slate-500 mb-4">Active background daemons status</p>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-slate-900/60 rounded-lg border border-slate-800">
                        <div class="flex items-center space-x-3">
                            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                            <span class="text-sm font-medium text-slate-200">Nginx / Web Server</span>
                        </div>
                        <span class="text-xs bg-emerald-950 text-emerald-400 px-2 py-1 rounded">Active</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-slate-900/60 rounded-lg border border-slate-800">
                        <div class="flex items-center space-x-3">
                            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                            <span class="text-sm font-medium text-slate-200">PostgreSQL Database</span>
                        </div>
                        <span class="text-xs bg-emerald-950 text-emerald-400 px-2 py-1 rounded">Active</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-slate-900/60 rounded-lg border border-slate-800">
                        <div class="flex items-center space-x-3">
                            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                            <span class="text-sm font-medium text-slate-200">PHP-FPM Worker</span>
                        </div>
                        <span class="text-xs bg-emerald-950 text-emerald-400 px-2 py-1 rounded">Active</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-slate-900/60 rounded-lg border border-slate-800">
                        <div class="flex items-center space-x-3">
                            <span class="w-2.5 h-2.5 bg-sky-500 rounded-full"></span>
                            <span class="text-sm font-medium text-slate-200">Tailscale Network Engine</span>
                        </div>
                        <span class="text-xs bg-sky-950 text-sky-400 px-2 py-1 rounded">Connected</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Performance History Chart -->
        <div class="bg-[#0f172a] border border-slate-800 p-6 rounded-xl">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider">SERVER LOAD HISTORY (PAST 12 HOURS)</h3>
                    <p class="text-xs text-slate-500">Historical performance metrics trend</p>
                </div>
                <span class="text-xs bg-slate-800 text-slate-400 px-3 py-1 rounded border border-slate-700">Auto-Refreshed</span>
            </div>
            <div class="h-64">
                <canvas id="historyChart"></canvas>
            </div>
        </div>

    </main>

    <!-- Script Chart.js Initialization -->
    <script>
        // 1. CPU Gauge Chart
        const cpuCtx = document.getElementById('cpuChart').getContext('2d');
        new Chart(cpuCtx, {
            type: 'doughnut',
            data: {
                labels: ['Used CPU', 'Free CPU'],
                datasets: [{
                    data: [{{ $metrics['cpu_usage'] }}, {{ 100 - $metrics['cpu_usage'] }}],
                    backgroundColor: ['#10b981', '#334155'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '78%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // 2. Storage & RAM Bar Chart
        const resCtx = document.getElementById('resourceChart').getContext('2d');
        new Chart(resCtx, {
            type: 'bar',
            data: {
                labels: ['RAM Usage (%)', 'Storage Usage (%)'],
                datasets: [{
                    label: 'Used %',
                    data: [{{ $metrics['ram_usage'] }}, {{ $metrics['disk_usage'] }}],
                    backgroundColor: ['#38bdf8', '#818cf8'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { max: 100, grid: { color: '#1e293b' }, ticks: { color: '#94a3b8' } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                },
                plugins: { legend: { display: false } }
            }
        });

        // 3. Performance History Area Line Chart
        const histCtx = document.getElementById('historyChart').getContext('2d');
        new Chart(histCtx, {
            type: 'line',
            data: {
                labels: ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00'],
                datasets: [
                    {
                        label: 'CPU Load (%)',
                        data: [15, 22, 18, 35, 42, 28, 20, 25, 30, 19, 24, {{ $metrics['cpu_usage'] }}],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'RAM Usage (%)',
                        data: [32, 34, 35, 38, 40, 39, 38, 37, 39, 40, 38, {{ $metrics['ram_usage'] }}],
                        borderColor: '#38bdf8',
                        backgroundColor: 'rgba(56, 189, 248, 0.05)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { max: 100, grid: { color: '#1e293b' }, ticks: { color: '#94a3b8' } },
                    x: { grid: { color: '#1e293b' }, ticks: { color: '#94a3b8' } }
                },
                plugins: {
                    legend: { labels: { color: '#94a3b8' } }
                }
            }
        });
    </script>
</body>
</html>