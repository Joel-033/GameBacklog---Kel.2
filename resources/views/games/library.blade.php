<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Library - GameBacklog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="wii-body" data-theme="classic">

<div class="library-page">

    <!-- Header -->
    <div class="library-header">

        <div>
            <a href="{{ route('games.index') }}" class="wii-back-button">
                ← Menu
            </a>
        </div>

        <div class="library-heading">
            <span class="library-icon">🎮</span>
            <h1>My Personal Library</h1>
            <p>Koleksi game pribadi kamu di GameBacklog.</p>
        </div>

        <button type="button"
                class="theme-toggle"
                id="wii-theme-toggle"
                aria-label="Ganti tema">
            🌙
        </button>

    </div>

    <!-- Ringkasan koleksi -->
    <div class="library-summary">
        <div class="library-summary-card">
            <span>Total Games</span>
            <strong>{{ $userGames->total() }}</strong>
        </div>
    </div>

    <!-- Statistik Koleksi: Horizontal Bar Chart & Doughnut Chart -->
    <section class="library-content" style="margin-bottom: 1.5rem;">
        <div class="library-section-heading">
            <h2>Library Statistics</h2>
            <span>Playtime &amp; Status Overview</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">

            <!-- Kotak 1: Horizontal Bar Chart -->
            <div class="library-summary-card" style="width: 100%; display: block; text-align: left; padding: 1.5rem;">
                <h3 style="margin-bottom: 1rem; font-size: 1rem; color: #38bdf8;">Top 5 Most Played Games (Hours)</h3>
                @if ($chartData->isEmpty())
                    <p style="text-align: center; opacity: 0.7; margin: 2.5rem 0; font-size: 0.9rem;">
                        Belum ada catatan waktu bermain (playtime &gt; 0 jam).
                    </p>
                @else
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="playtimeBarChart"></canvas>
                    </div>
                @endif
            </div>

            <!-- Kotak 2: Doughnut Chart -->
            <div class="library-summary-card" style="width: 100%; display: block; text-align: left; padding: 1.5rem;">
                <h3 style="margin-bottom: 1rem; font-size: 1rem; color: #38bdf8;">Game Status Distribution</h3>
                @if ($statusData->isEmpty())
                    <p style="text-align: center; opacity: 0.7; margin: 2.5rem 0; font-size: 0.9rem;">
                        Belum ada game di koleksimu.
                    </p>
                @else
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="statusDoughnutChart"></canvas>
                    </div>
                @endif
            </div>

        </div>
    </section>

    <!-- Daftar game -->
    <section class="library-content">

        @if (session('success'))
            <div class="wii-success-message">
                {{ session('success') }}
            </div>
        @endif

        <div class="library-section-heading">
            <h2>My Games</h2>
            <span>{{ $userGames->total() }} Games</span>
        </div>

        @if ($userGames->isEmpty())

            <div class="library-empty">
                <div>🎮</div>
                <h3>Library kamu masih kosong</h3>
                <p>Tambahkan game dari Menu untuk mulai membangun koleksimu.</p>

                <a href="{{ route('games.index') }}" class="wii-edit-submit">
                    Browse Games
                </a>
            </div>

        @else

            <div class="library-grid">

                @foreach ($userGames as $userGame)

                    @php
                        $coverImage = trim((string) ($userGame->game->cover_image ?? ''));

                        if ($coverImage === '') {
                            $coverUrl = null;
                        } elseif (filter_var($coverImage, FILTER_VALIDATE_URL)) {
                            $coverUrl = $coverImage;
                        } elseif (str_starts_with($coverImage, '/storage/')) {
                            $coverUrl = asset(ltrim($coverImage, '/'));
                        } elseif (str_starts_with($coverImage, 'storage/')) {
                            $coverUrl = asset($coverImage);
                        } else {
                            $coverUrl = asset('storage/' . ltrim($coverImage, '/'));
                        }
                    @endphp

                    <article class="library-game-card">

                        <!-- Cover Game -->
                        <a href="{{ route('games.show', $userGame->game->id) }}"
                           class="library-game-cover">

                            @if ($coverUrl)

                                <img
                                    src="{{ $coverUrl }}"
                                    alt="Cover {{ $userGame->game->title }}"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >

                                <div class="library-no-cover" style="display: none;">
                                    🎮
                                </div>

                            @else

                                <div class="library-no-cover">
                                    🎮
                                </div>

                            @endif

                        </a>

                        <!-- Informasi Game -->
                        <div class="library-game-info">

                            <h3>
                                {{ $userGame->game->title }}
                            </h3>

                            <div class="library-game-meta">

                                <div>
                                    <span>Status</span>
                                    <strong class="library-status">
                                        {{ $userGame->status }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Platform</span>
                                    <strong>
                                        {{ $userGame->platform->name ?? 'Belum dipilih' }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Playtime</span>
                                    <strong>
                                        {{ $userGame->playtime }} Hours
                                    </strong>
                                </div>

                                <div>
                                    <span>Priority</span>
                                    <strong>
                                        {{ $userGame->priority ?? '-' }}
                                    </strong>
                                </div>

                            </div>

                            <!-- Tombol Aksi -->
                            <div class="library-card-actions">

                                <a href="{{ route('games.show', $userGame->game->id) }}"
                                   class="library-detail-button">
                                    View Details
                                </a>

                                <a href="{{ route('games.library.edit', $userGame->id) }}"
                                   class="library-detail-button">
                                    ✎ Edit Library
                                </a>

                                <form action="{{ route('games.library.remove', $userGame->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Apakah kamu yakin ingin menghapus game ini dari Personal Library?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="library-remove-button">
                                        ✕ Remove
                                    </button>

                                </form>

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>

            <!-- Pagination -->
            <div class="library-pagination">
                {{ $userGames->links() }}
            </div>

        @endif

    </section>

    <footer class="library-footer">
        GAMEBACKLOG · MY PERSONAL COLLECTION
    </footer>

</div>

<!-- Theme Toggle -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const themeToggle = document.getElementById('wii-theme-toggle');
    const savedTheme = localStorage.getItem('wii-theme') || 'classic';

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
        const currentTheme = document.body.dataset.theme;
        applyTheme(currentTheme === 'classic' ? 'midnight' : 'classic');
    });

});
</script>

<!-- Chart.js CDN & Render Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let playtimeChart = null;
    let statusChart = null;

    function getThemeColors() {
        const isMidnight = document.body.dataset.theme === 'midnight';
        return {
            textColor: isMidnight ? '#e2e8f0' : '#334155',
            subTextColor: isMidnight ? '#94a3b8' : '#64748b',
            gridColor: isMidnight ? 'rgba(148, 163, 184, 0.12)' : 'rgba(100, 116, 139, 0.15)',
            barBg: isMidnight ? 'rgba(56, 189, 248, 0.75)' : 'rgba(14, 165, 233, 0.8)',
            barBorder: isMidnight ? '#38bdf8' : '#0284c7',
            tooltipBg: isMidnight ? '#0f172a' : '#1e293b',
            donutBorder: isMidnight ? '#112236' : '#ffffff'
        };
    }

    const statusColorMap = {
        'Playing':      '#38bdf8',
        'Completed':    '#34d399',
        'Backlog':      '#94a3b8',
        'Plan to Play': '#a78bfa',
        'Dropped':      '#f87171'
    };

    function renderCharts() {
        const colors = getThemeColors();

        const barCanvas = document.getElementById('playtimeBarChart');
        if (barCanvas) {
            if (playtimeChart) playtimeChart.destroy();
            playtimeChart = new Chart(barCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Playtime (Hours)',
                        data: @json($chartData),
                        backgroundColor: colors.barBg,
                        borderColor: colors.barBorder,
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barThickness: 22
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: colors.tooltipBg,
                            titleColor: '#38bdf8',
                            bodyColor: '#f8fafc',
                            padding: 10,
                            callbacks: {
                                label: (ctx) => ' ' + ctx.parsed.x + ' Hours'
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: colors.gridColor, drawBorder: false },
                            ticks: {
                                color: colors.subTextColor,
                                callback: (val) => val + 'h'
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                color: colors.textColor,
                                font: { weight: '600', size: 12 }
                            }
                        }
                    }
                }
            });
        }

        const donutCanvas = document.getElementById('statusDoughnutChart');
        if (donutCanvas) {
            if (statusChart) statusChart.destroy();
            const rawLabels = @json($statusLabels);
            const sliceColors = rawLabels.map(label => statusColorMap[label] || '#38bdf8');

            statusChart = new Chart(donutCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: rawLabels,
                    datasets: [{
                        data: @json($statusData),
                        backgroundColor: sliceColors,
                        borderColor: colors.donutBorder,
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: colors.textColor,
                                padding: 14,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 12, weight: '500' }
                            }
                        },
                        tooltip: {
                            backgroundColor: colors.tooltipBg,
                            titleColor: '#38bdf8',
                            bodyColor: '#f8fafc',
                            padding: 10,
                            callbacks: {
                                label: (ctx) => ' ' + ctx.label + ': ' + ctx.parsed + ' Games'
                            }
                        }
                    }
                }
            });
        }
    }

    renderCharts();

    const themeToggleBtn = document.getElementById('wii-theme-toggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function () {
            setTimeout(renderCharts, 50);
        });
    }
});
</script>

</body>
</html>