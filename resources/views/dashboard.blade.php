<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - GameBacklog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ADMIN ANALYTICS PANEL */
        .admin-analytics-section {
            margin-top: 1.75rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(56, 189, 248, 0.25);
        }

        .admin-analytics-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            margin-top: 1rem;
        }

        .admin-widget-card {
            background: rgba(255, 255, 255, 0.65);
            border: 1px solid rgba(14, 165, 233, 0.22);
            border-radius: 16px;
            padding: 1.25rem 1.4rem;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
        }

        body[data-theme="midnight"] .admin-widget-card {
            background: rgba(15, 35, 52, 0.72);
            border-color: rgba(88, 207, 255, 0.25);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.28);
        }

        .admin-widget-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .admin-widget-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #0284c7;
        }

        body[data-theme="midnight"] .admin-widget-title {
            color: #38bdf8;
        }

        .admin-period-select {
            padding: 7px 14px;
            border-radius: 999px;
            border: 1px solid #38bdf8;
            background: #f0f9ff;
            color: #0369a1;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            outline: none;
            transition: 0.2s;
        }

        body[data-theme="midnight"] .admin-period-select {
            background: rgba(18, 48, 65, 0.95);
            color: #70dcff;
            border-color: #58cfff;
        }

        .admin-period-select:focus {
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.25);
        }

        .admin-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .admin-top-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
            text-align: left;
        }

        .admin-top-table th {
            padding: 10px 12px;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            border-bottom: 1px solid rgba(148, 163, 184, 0.25);
        }

        body[data-theme="midnight"] .admin-top-table th {
            color: #94a3b8;
        }

        .admin-top-table td {
            padding: 11px 12px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.15);
            color: #1e293b;
        }

        body[data-theme="midnight"] .admin-top-table td {
            color: #e2e8f0;
        }

        .admin-top-table tr:last-child td {
            border-bottom: none;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            font-weight: 800;
            font-size: 12px;
            background: rgba(14, 165, 233, 0.14);
            color: #0284c7;
        }

        body[data-theme="midnight"] .rank-badge {
            background: rgba(56, 189, 248, 0.2);
            color: #38bdf8;
        }

        .rank-badge.top-1 {
            background: #f59e0b;
            color: #ffffff;
        }

        .popularity-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
        }

        body[data-theme="midnight"] .popularity-pill {
            background: rgba(52, 211, 153, 0.18);
            color: #34d399;
        }
    </style>
</head>

<body class="wii-body dashboard-page" data-theme="classic">

    <main class="dashboard-container">

        <!-- Top Bar -->
        <header class="dashboard-top">

            <span class="wii-brand">GAMEBACKLOG</span>

            <div class="dashboard-top-actions">

                <span class="dashboard-role">
                    {{ Auth::user()->role }}
                </span>

                <button type="button"
                        class="theme-toggle"
                        id="wii-theme-toggle"
                        title="Aktifkan Wii Midnight">
                    🌙
                </button>

            </div>

        </header>


        <!-- Welcome Card -->
        <section class="dashboard-card">

            <div class="dashboard-welcome">

                <div class="dashboard-avatar">
                    🎮
                </div>

                <p class="dashboard-subtitle">
                    GAMEBACKLOG · WII COLLECTION SYSTEM
                </p>

                <h1>Welcome Back!</h1>

                <h2>
                    Hello, {{ Auth::user()->name }}!
                </h2>

                <p class="dashboard-description">
                    Your gaming journey continues here.
                    Explore your collection and enjoy your games.
                </p>

            </div>


            <!-- Main Navigation -->
            <div class="dashboard-actions">

                <a href="{{ route('games.index') }}"
                   class="dashboard-menu-card">

                    <div class="dashboard-menu-icon">
                        🎮
                    </div>

                    <div>
                        <h3>My Collection</h3>
                        <p>Explore your game collection</p>
                    </div>

                    <span class="dashboard-arrow">›</span>

                </a>


                <div class="dashboard-menu-card account-card">

                    <div class="dashboard-menu-icon">
                        👤
                    </div>

                    <div>
                        <h3>My Account</h3>
                        <p>View your account information</p>
                    </div>

                    <span class="dashboard-arrow">›</span>

                </div>

            </div>


            <!-- Account Information -->
            <div class="dashboard-account">

                <div class="dashboard-section-heading">
                    <h3>Account Information</h3>
                    <span>PROFILE</span>
                </div>

                <div class="dashboard-info-grid">

                    <div class="dashboard-info-item">
                        <span>Full Name</span>
                        <strong>{{ Auth::user()->name }}</strong>
                    </div>

                    <div class="dashboard-info-item">
                        <span>Email Address</span>
                        <strong>{{ Auth::user()->email }}</strong>
                    </div>

                    <div class="dashboard-info-item">
                        <span>Account Role</span>
                        <strong>{{ Auth::user()->role }}</strong>
                    </div>

                </div>

            </div>


            <!-- ADMIN ANALYTICS: KHUSUS ROLE ADMIN -->
            @if (Auth::user()->role === 'admin' && !empty($adminAnalytics))
                <div class="admin-analytics-section">

                    <div class="dashboard-section-heading">
                        <h3>Admin Analytics</h3>
                        <span>CATALOG &amp; POPULARITY</span>
                    </div>

                    <div class="admin-analytics-grid">

                        <!-- 1. Bar Chart Distribusi Game per Genre -->
                        <div class="admin-widget-card">
                            <div class="admin-widget-header">
                                <h4 class="admin-widget-title">📊 Distribusi Game per Genre</h4>
                            </div>

                            @if ($adminAnalytics['genreCounts']->isEmpty())
                                <p style="text-align: center; opacity: 0.7; margin: 2rem 0; font-size: 13.5px;">
                                    Belum ada data genre di katalog.
                                </p>
                            @else
                                <div style="position: relative; height: 240px; width: 100%;">
                                    <canvas id="adminGenreBarChart"></canvas>
                                </div>
                            @endif
                        </div>

                        <!-- 2. Ringkasan Top 5 Game Terpopuler di Library -->
                        <div class="admin-widget-card">
                            <div class="admin-widget-header">
                                <h4 class="admin-widget-title">🔥 Top 5 Game Terpopuler di Library</h4>

                                <select id="top-games-period" class="admin-period-select" aria-label="Pilih rentang waktu">
                                    <option value="day" selected>Per Day (Hari Ini)</option>
                                    <option value="week">Per Week (7 Hari)</option>
                                    <option value="month">Per Month (30 Hari)</option>
                                </select>
                            </div>

                            <div class="admin-table-wrapper">
                                <table class="admin-top-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">Rank</th>
                                            <th>Judul Game</th>
                                            <th>Genre</th>
                                            <th style="text-align: right;">Ditambahkan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="top-games-tbody">
                                        <!-- Diisi otomatis via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            @endif


            <!-- Bottom Actions -->
            <div class="dashboard-bottom-actions">

                <a href="{{ route('games.index') }}"
                   class="dashboard-enter-button">

                    Open Menu

                    <span>→</span>

                </a>


                <form action="/logout" method="POST">
                    @csrf

                    <button type="submit"
                            class="dashboard-logout-button"
                            onclick="return confirm('Apakah kamu yakin ingin logout?')">

                        <span>↪</span>
                        Logout

                    </button>

                </form>

            </div>


            <!-- Footer -->
            <footer class="dashboard-footer">
                GAMEBACKLOG · WII COLLECTION SYSTEM
            </footer>

        </section>


        <div class="dashboard-bottom-text">
            YOUR GAME COLLECTION STARTS HERE
        </div>

    </main>


    <!-- Theme Switcher -->
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const themeToggle =
                document.getElementById('wii-theme-toggle');

            const savedTheme =
                localStorage.getItem('wii-theme') || 'classic';


            function applyTheme(theme) {

                document.body.dataset.theme = theme;

                localStorage.setItem('wii-theme', theme);


                if (theme === 'midnight') {

                    themeToggle.textContent = '☀️';

                    themeToggle.title = 'Aktifkan Wii Classic';

                } else {

                    themeToggle.textContent = '🌙';

                    themeToggle.title = 'Aktifkan Wii Midnight';

                }

            }


            applyTheme(savedTheme);


            themeToggle.addEventListener('click', function () {

                const currentTheme =
                    document.body.dataset.theme;

                const newTheme =
                    currentTheme === 'classic'
                        ? 'midnight'
                        : 'classic';

                applyTheme(newTheme);

            });

        });

    </script>


    <!-- Admin Analytics Chart & Top 5 Filter Script -->
    @if (Auth::user()->role === 'admin' && !empty($adminAnalytics))
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. RENDER BAR CHART DISTRIBUSI GENRE
            let genreChart = null;
            const genreLabels = @json($adminAnalytics['genreLabels']);
            const genreCounts = @json($adminAnalytics['genreCounts']);

            function renderAdminGenreChart() {
                const canvas = document.getElementById('adminGenreBarChart');
                if (!canvas) return;

                if (genreChart) genreChart.destroy();

                const isMidnight = document.body.dataset.theme === 'midnight';
                const textColor = isMidnight ? '#e2e8f0' : '#334155';
                const subTextColor = isMidnight ? '#94a3b8' : '#64748b';
                const gridColor = isMidnight ? 'rgba(148, 163, 184, 0.14)' : 'rgba(100, 116, 139, 0.15)';

                genreChart = new Chart(canvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: genreLabels,
                        datasets: [{
                            label: 'Jumlah Game',
                            data: genreCounts,
                            backgroundColor: isMidnight ? 'rgba(56, 189, 248, 0.78)' : 'rgba(14, 165, 233, 0.85)',
                            borderColor: isMidnight ? '#38bdf8' : '#0284c7',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            maxBarThickness: 36
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#38bdf8',
                                bodyColor: '#f8fafc',
                                padding: 10,
                                callbacks: {
                                    label: (ctx) => ' ' + ctx.parsed.y + ' Games'
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    color: textColor,
                                    font: { size: 11.5, weight: '600' }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: gridColor, drawBorder: false },
                                ticks: {
                                    color: subTextColor,
                                    stepSize: 1,
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            renderAdminGenreChart();

            const themeBtn = document.getElementById('wii-theme-toggle');
            if (themeBtn) {
                themeBtn.addEventListener('click', () => {
                    setTimeout(renderAdminGenreChart, 50);
                });
            }

            // 2. RENDER TABEL TOP 5 (DEFAULT: DAY, DROPDOWN: DAY / WEEK / MONTH)
            const topDatasets = {
                day:   @json($adminAnalytics['topDay']),
                week:  @json($adminAnalytics['topWeek']),
                month: @json($adminAnalytics['topMonth']),
            };

            const periodLabels = {
                day:   'hari ini',
                week:  '7 hari terakhir',
                month: '30 hari terakhir',
            };

            const periodSelect = document.getElementById('top-games-period');
            const tableBody = document.getElementById('top-games-tbody');

            function renderTopTable(period) {
                if (!tableBody) return;
                const rows = topDatasets[period] || [];

                if (rows.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="4" style="text-align: center; opacity: 0.7; padding: 1.5rem;">
                                Belum ada game yang ditambahkan ke library pada periode ${periodLabels[period]}.
                            </td>
                        </tr>
                    `;
                    return;
                }

                tableBody.innerHTML = rows.map(item => `
                    <tr>
                        <td>
                            <span class="rank-badge ${item.rank === 1 ? 'top-1' : ''}">
                                #${item.rank}
                            </span>
                        </td>
                        <td style="font-weight: 700;">${item.title}</td>
                        <td style="opacity: 0.85;">${item.genre}</td>
                        <td style="text-align: right;">
                            <span class="popularity-pill">${item.count} User</span>
                        </td>
                    </tr>
                `).join('');
            }

            // Render default: Per Day
            renderTopTable('day');

            if (periodSelect) {
                periodSelect.addEventListener('change', function () {
                    renderTopTable(this.value);
                });
            }
        });
        </script>
    @endif

</body>
</html>