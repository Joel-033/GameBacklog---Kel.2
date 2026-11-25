<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommendations - GameBacklog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .rec-subtitle {
            margin: 6px 0 18px 0;
            font-size: 13px;
            opacity: 0.85;
        }

        .rec-highlight {
            color: #299fd6;
            font-weight: 700;
        }

        body[data-theme="midnight"] .rec-highlight {
            color: #38bdf8;
        }

        .rec-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 16px;
            margin-top: 12px;
        }

        .rec-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 14px !important;
            border-radius: 14px;
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .rec-card:hover {
            transform: translateY(-3px);
            border-color: #38bdf8;
        }

        .rec-cover-wrap {
            position: relative;
            width: 100%;
            height: 135px;
            border-radius: 10px;
            overflow: hidden;
            background: rgba(15, 23, 42, 0.15);
            margin-bottom: 12px;
        }

        .rec-cover-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .rec-rating-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(15, 23, 42, 0.85);
            color: #fbbf24;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 999px;
            border: 1px solid rgba(251, 191, 36, 0.35);
        }

        .rec-title {
            margin: 0 0 6px 0;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.3;
        }

        .rec-meta {
            margin: 0;
            font-size: 12px;
            opacity: 0.75;
        }

        .rec-tag {
            display: inline-block;
            margin-top: 12px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            background: rgba(41, 159, 214, 0.14);
            color: #299fd6;
            border: 1px solid rgba(41, 159, 214, 0.3);
        }

        body[data-theme="midnight"] .rec-tag {
            background: rgba(56, 189, 248, 0.14);
            color: #38bdf8;
            border-color: rgba(56, 189, 248, 0.3);
        }

        .rec-status-box {
            grid-column: 1 / -1;
            text-align: center;
            padding: 28px 16px;
            font-size: 13px;
            opacity: 0.8;
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
                    @auth
                        {{ Auth::user()->role }}
                    @else
                        GUEST
                    @endauth
                </span>

                <button type="button"
                        class="theme-toggle"
                        id="wii-theme-toggle"
                        title="Aktifkan Wii Midnight">
                    🌙
                </button>
            </div>
        </header>

        <!-- Main Card -->
        <section class="dashboard-card">

            <div class="dashboard-account">
                <div class="dashboard-section-heading">
                    <h3>Rekomendasi Game Untukmu</h3>
                    <span id="source-badge">LOADING API...</span>
                </div>

                <p id="subtitle-text" class="rec-subtitle">
                    Menganalisis riwayat game dan genre favoritmu...
                </p>

                <div id="recommendations-grid" class="rec-grid">
                    <div class="dashboard-info-item rec-status-box">
                        Memuat rekomendasi game dari API...
                    </div>
                </div>
            </div>

            <!-- Bottom Navigation -->
            <div class="dashboard-bottom-actions">
                <a href="{{ route('dashboard') }}" class="dashboard-enter-button">
                    <span>←</span>
                    Kembali ke Dashboard
                </a>

                <a href="{{ route('games.index') }}" class="dashboard-enter-button">
                    My Collection
                    <span>→</span>
                </a>
            </div>

            <!-- Footer -->
            <footer class="dashboard-footer">
                GAMEBACKLOG · WII COLLECTION SYSTEM · POWERED BY RAWG API
            </footer>

        </section>

        <div class="dashboard-bottom-text">
            PERSONALIZED GAME RECOMMENDATIONS
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Sinkronisasi tema Wii Classic / Midnight dengan Dashboard
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
                const newTheme = document.body.dataset.theme === 'classic' ? 'midnight' : 'classic';
                applyTheme(newTheme);
            });

            // 2. Fetch Data dari API Rekomendasi Buatan Sendiri
            const userId = {{ auth()->id() ?? 1 }};
            const grid = document.getElementById('recommendations-grid');
            const subtitle = document.getElementById('subtitle-text');
            const sourceBadge = document.getElementById('source-badge');

            fetch(`/api/users/${userId}/recommendations`)
                .then(response => response.json())
                .then(res => {
                    if (res.status !== 'success') {
                        throw new Error(res.message || 'Gagal memuat data');
                    }

                    subtitle.innerHTML = `Berdasarkan aktivitas bermainmu pada genre <span class="rec-highlight">${res.based_on_favorite_genre}</span>`;
                    sourceBadge.textContent = res.source.toUpperCase();

                    if (!res.recommendations || res.recommendations.length === 0) {
                        grid.innerHTML = `
                            <div class="dashboard-info-item rec-status-box">
                                Belum ada rekomendasi baru untuk genre ini.
                            </div>
                        `;
                        return;
                    }

                    grid.innerHTML = res.recommendations.map(game => {
                        const cover = game.cover_image || 'https://placehold.co/600x400/1e293b/38bdf8?text=No+Cover';
                        const releaseDate = game.released || (game.release_date ? game.release_date.split('T')[0] : 'TBA');
                        const rating = game.rating ? `⭐ ${game.rating}` : '⭐ N/A';

                        return `
                            <div class="dashboard-info-item rec-card">
                                <div>
                                    <div class="rec-cover-wrap">
                                        <img src="${cover}" alt="${game.title}">
                                        <span class="rec-rating-badge">${rating}</span>
                                    </div>
                                    <h4 class="rec-title">${game.title}</h4>
                                    <p class="rec-meta">Rilis: ${releaseDate}</p>
                                </div>
                                <span class="rec-tag">Cocok dengan seleramu</span>
                            </div>
                        `;
                    }).join('');
                })
                .catch(err => {
                    sourceBadge.textContent = 'API ERROR';
                    grid.innerHTML = `
                        <div class="dashboard-info-item rec-status-box" style="color: #ef4444;">
                            Gagal memuat rekomendasi: ${err.message}
                        </div>
                    `;
                });
        });
    </script>

</body>
</html>