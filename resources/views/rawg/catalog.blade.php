
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RAWG Game Catalog - GameBacklog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* =================================
           RAWG CATALOG - WII DESIGN
        ================================= */

        .rawg-page {
            --bg: #eaf8ff;
            --surface: rgba(255,255,255,.88);
            --card: #ffffff;
            --text: #17374a;
            --muted: #64869a;
            --accent: #35c9f5;
            --border: #b6eaff;
            --shadow: 0 7px 20px rgba(40,120,160,.15);

            min-height: 100vh;
            padding: 35px 24px 50px;
            background: linear-gradient(145deg, #f7fdff, #dff4ff);
            color: var(--text);
            transition: background .3s, color .3s;
        }

        .rawg-page[data-theme="midnight"] {
            --bg: #101f2c;
            --surface: rgba(28,49,65,.95);
            --card: #1b3040;
            --text: #f0faff;
            --muted: #a3c3d7;
            --accent: #48d4ff;
            --border: #315c72;
            --shadow: 0 8px 22px rgba(0,0,0,.3);

            background: linear-gradient(145deg, #0c1925, #193448);
        }

        .rawg-container {
            max-width: 1150px;
            margin: auto;
        }

        /* HEADER */

        .rawg-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 35px;
        }

        .rawg-logo {
            color: var(--accent);
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 5px;
            text-decoration: none;
        }

        .rawg-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .rawg-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 7px;

            padding: 11px 19px;
            border: 1px solid var(--accent);
            border-radius: 30px;

            background: var(--surface);
            color: var(--text);

            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            cursor: pointer;

            box-shadow: var(--shadow);
            transition: .25s;
        }

        .rawg-button:hover {
            background: var(--accent);
            color: #103044;
            transform: translateY(-2px);
        }

        .rawg-theme-button {
            width: 43px;
            height: 43px;
            border-radius: 50%;
            font-size: 19px;
            padding: 0;
        }

        /* HEADING */

        .rawg-heading {
            text-align: center;
            margin-bottom: 27px;
        }

        .rawg-heading-icon {
            font-size: 43px;
            margin-bottom: 8px;
        }

        .rawg-heading h1 {
            font-size: 32px;
            font-weight: 800;
            color: var(--accent);
            margin: 0 0 8px;
        }

        .rawg-heading p {
            color: var(--muted);
            margin: 0;
            font-size: 14px;
        }

        /* SEARCH */

        .rawg-search {
            max-width: 680px;
            margin: 0 auto 35px;
        }

        .rawg-search form {
            display: flex;
            gap: 10px;
        }

        .rawg-search input {
            flex: 1;
            min-width: 0;
            padding: 13px 19px;

            border: 1px solid var(--accent);
            border-radius: 30px;

            outline: none;
            background: var(--surface);
            color: var(--text);

            font-size: 14px;
            box-shadow: var(--shadow);
        }

        .rawg-search input::placeholder {
            color: var(--muted);
        }

        .rawg-search input:focus {
            box-shadow: 0 0 0 3px rgba(53,201,245,.2);
        }

        .rawg-search button {
            padding: 12px 23px;
            border: 1px solid var(--accent);
            border-radius: 30px;

            background: var(--accent);
            color: #123044;

            font-weight: 800;
            cursor: pointer;
            transition: .2s;
        }

        .rawg-search button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(53,201,245,.3);
        }

        /* SEARCH INFO */

        .rawg-results-info {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 17px;
        }

        /* ALERT */

        .rawg-alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 22px;
            background: rgba(255,70,70,.12);
            border: 1px solid #e88a8a;
            color: var(--text);
        }

        /* GAME GRID */

        .rawg-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
        }

        .rawg-card {
            display: block;
            min-width: 0;

            overflow: hidden;
            position: relative;

            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;

            color: var(--text);
            text-decoration: none;

            box-shadow: var(--shadow);
            transition: transform .25s, box-shadow .25s;
        }

        .rawg-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(30,150,200,.25);
        }

        .rawg-card-image {
            width: 100%;
            height: 165px;
            object-fit: cover;
            display: block;
            background: var(--bg);
        }

        .rawg-no-image {
            height: 165px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            color: var(--muted);
            font-size: 40px;
        }

        .rawg-card-content {
            padding: 14px;
        }

        .rawg-card-title {
            font-size: 15px;
            font-weight: 800;
            line-height: 1.4;
            margin: 0 0 10px;

            min-height: 42px;

            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .rawg-card-meta {
            display: flex;
            justify-content: space-between;
            gap: 8px;

            color: var(--muted);
            font-size: 12px;
        }

        .rawg-rating {
            color: #e6a52e;
            font-weight: 800;
        }

        .rawg-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;

            padding: 10px 14px;
            border-top: 1px solid var(--border);

            font-size: 12px;
            font-weight: 700;
            color: var(--accent);
        }

        /* EMPTY STATE */

        .rawg-empty {
            padding: 45px 20px;
            border-radius: 18px;

            background: var(--surface);
            border: 1px solid var(--border);
            text-align: center;

            box-shadow: var(--shadow);
        }

        .rawg-empty-icon {
            font-size: 45px;
            margin-bottom: 10px;
        }

        .rawg-empty h3 {
            color: var(--accent);
            margin-bottom: 8px;
        }

        .rawg-empty p {
            color: var(--muted);
            margin: 0;
        }

        /* FOOTER */

        .rawg-footer {
            margin-top: 45px;
            text-align: center;
            color: var(--muted);
            font-size: 12px;
        }

        .rawg-footer a {
            color: var(--accent);
        }

        /* RESPONSIVE */

        @media (max-width: 900px) {
            .rawg-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 650px) {
            .rawg-page {
                padding: 22px 14px 35px;
            }

            .rawg-header {
                flex-wrap: wrap;
            }

            .rawg-logo {
                font-size: 16px;
            }

            .rawg-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .rawg-card-image,
            .rawg-no-image {
                height: 125px;
            }

            .rawg-card-content {
                padding: 10px;
            }

            .rawg-card-title {
                font-size: 13px;
            }

            .rawg-heading h1 {
                font-size: 25px;
            }
        }

        @media (max-width: 380px) {
            .rawg-header-actions {
                gap: 6px;
            }

            .rawg-button {
                padding: 9px 12px;
                font-size: 11px;
            }

            .rawg-search form {
                flex-wrap: wrap;
            }

            .rawg-search button {
                width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .rawg-page *,
            .rawg-page *::before,
            .rawg-page *::after {
                transition: none !important;
            }
        }


        /* =====================================
        FIX: RAWG CATALOG SCROLLING
        ===================================== */

        /* Izinkan browser melakukan scrolling */
        html {
            height: auto !important;
            min-height: 100%;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        body {
            height: auto !important;
            min-height: 100vh;
            margin: 0;

            overflow-x: hidden !important;
            overflow-y: auto !important;

            position: relative !important;
        }

        /* Pastikan halaman tidak terkunci pada tinggi layar */
        .rawg-page {
            height: auto !important;
            min-height: 100vh;

            overflow: visible !important;
            position: relative !important;
        }

        /* Container mengikuti tinggi seluruh konten */
        .rawg-container {
            height: auto !important;
            overflow: visible !important;
        }

        /* Pastikan grid dapat memanjang ke bawah */
        .rawg-grid {
            height: auto !important;
            overflow: visible !important;
        }

        /* Berikan ruang untuk card terakhir */
        .rawg-footer {
            padding-bottom: 25px;
        }

    </style>
</head>

<body>

<div class="rawg-page" id="rawg-page" data-theme="classic">

    <div class="rawg-container">

        {{-- HEADER --}}
        <header class="rawg-header">

            <a href="{{ route('games.index') }}" class="rawg-logo">
                GAMEBACKLOG
            </a>

            <div class="rawg-header-actions">

                <a href="{{ route('games.index') }}"
                   class="rawg-button">
                    ← Menu
                </a>

                <button type="button"
                        class="rawg-button rawg-theme-button"
                        id="rawg-theme-toggle"
                        aria-label="Ganti tema">
                    🌙
                </button>

            </div>

        </header>


        {{-- HEADING --}}
        <section class="rawg-heading">

            <div class="rawg-heading-icon">🎮</div>

            <h1>RAWG Game Catalog</h1>

            <p>
                Temukan game favoritmu melalui database RAWG.
            </p>

        </section>


        {{-- SEARCH --}}
        <section class="rawg-search">

            <form action="{{ url('/rawg-catalog') }}"
                  method="GET">

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="🔎 Cari judul game..."
                    autocomplete="off"
                    required
                >

                <button type="submit">
                    🔍 Search
                </button>

            </form>

        </section>


        {{-- ERROR --}}
        @if ($error)
            <div class="rawg-alert">
                ⚠️ {{ $error }}
            </div>
        @endif


        {{-- RESULTS --}}
        @if (trim($search) !== '')

            @if (count($games) > 0)

                <div class="rawg-results-info">
                    Menampilkan {{ count($games) }} hasil
                    untuk pencarian:
                    <strong>{{ $search }}</strong>
                </div>

                <div class="rawg-grid">

                    @foreach ($games as $game)

                        <a href="{{ route('rawg.detail', [
                            'id' => $game['id'],
                            'search' => $search
                        ]) }}"
                           class="rawg-card">

                            @if (!empty($game['background_image']))

                                <img
                                    src="{{ $game['background_image'] }}"
                                    alt="{{ $game['name'] }}"
                                    class="rawg-card-image"
                                    loading="lazy"
                                >

                            @else

                                <div class="rawg-no-image">
                                    🎮
                                </div>

                            @endif

                            <div class="rawg-card-content">

                                <h3 class="rawg-card-title">
                                    {{ $game['name'] }}
                                </h3>

                                <div class="rawg-card-meta">

                                    <span>
                                        📅
                                        {{ $game['released'] ?? 'TBA' }}
                                    </span>

                                    <span class="rawg-rating">
                                        ⭐
                                        {{ $game['rating'] ?? 'N/A' }}
                                    </span>

                                </div>

                            </div>

                            <div class="rawg-card-footer">

                                <span>View Details</span>

                                <span>→</span>

                            </div>

                        </a>

                    @endforeach

                </div>

            @else

                <div class="rawg-empty">

                    <div class="rawg-empty-icon">🎮</div>

                    <h3>Game Tidak Ditemukan</h3>

                    <p>
                        Tidak ada hasil untuk
                        "{{ $search }}".
                        Coba gunakan kata kunci lain.
                    </p>

                </div>

            @endif

        @else

            <div class="rawg-empty">

                <div class="rawg-empty-icon">🔎</div>

                <h3>Explore Game Catalog</h3>

                <p>
                    Masukkan judul game untuk memulai pencarian.
                </p>

            </div>

        @endif


        {{-- FOOTER --}}
        <footer class="rawg-footer">

            <p>
                GAMEBACKLOG · RAWG GAME DATABASE
            </p>

            <p>
                Game data and images provided by
                <a href="https://rawg.io/"
                   target="_blank"
                   rel="noopener noreferrer">
                    RAWG
                </a>.
            </p>

        </footer>

    </div>

</div>


{{-- THEME SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const page = document.getElementById('rawg-page');
    const toggle = document.getElementById('rawg-theme-toggle');

    const savedTheme = localStorage.getItem('wii-theme') || 'classic';

    function applyTheme(theme) {

        page.dataset.theme = theme;

        localStorage.setItem('wii-theme', theme);

        if (theme === 'midnight') {

            toggle.textContent = '☀️';
            toggle.title = 'Aktifkan Wii Classic';

        } else {

            toggle.textContent = '🌙';
            toggle.title = 'Aktifkan Wii Midnight';

        }
    }

    applyTheme(savedTheme);

    toggle.addEventListener('click', function () {

        const currentTheme = page.dataset.theme;

        applyTheme(
            currentTheme === 'classic'
                ? 'midnight'
                : 'classic'
        );

    });

});
</script>

</body>
</html>
