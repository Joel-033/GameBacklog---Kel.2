<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GameBacklog - Menu</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* SEARCH BAR & FILTER BUTTON */
        .wii-search-wrapper {
            display: flex;
            justify-content: center;
            width: 100%;
            padding: 15px 20px 25px;
        }

        .wii-search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            max-width: 720px;
        }

        .wii-search-container {
            position: relative;
            flex: 1;
            min-width: 0;
            display: flex;
        }

        .wii-search-input {
            width: 100%;
            flex: 1;
            min-width: 0;
            padding: 13px 18px;
            border: 1px solid #58cfff;
            border-radius: 30px;
            outline: none;
            background: rgba(15, 35, 50, 0.9);
            color: #fff;
            font-size: 14px;
            transition: 0.2s;
        }

        .wii-search-input:focus {
            border-color: #65dfff;
            box-shadow: 0 0 10px rgba(70, 200, 255, 0.3);
        }

        .wii-search-input::placeholder {
            color: #a7c4d5;
        }

        /* LIVE SEARCH AUTOCOMPLETE DROPDOWN */
        .live-search-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: rgba(15, 35, 50, 0.97);
            border: 1px solid #58cfff;
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45);
            z-index: 999;
            overflow: hidden;
            display: none;
            backdrop-filter: blur(10px);
        }

        body[data-theme="classic"] .live-search-dropdown {
            background: rgba(255, 255, 255, 0.98);
            border-color: #38bdf8;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.15);
        }

        .live-search-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            text-decoration: none;
            color: #f1f5f9;
            border-bottom: 1px solid rgba(88, 207, 255, 0.15);
            transition: background 0.15s ease;
        }

        body[data-theme="classic"] .live-search-item {
            color: #1e293b;
            border-bottom-color: rgba(14, 165, 233, 0.15);
        }

        .live-search-item:last-child {
            border-bottom: none;
        }

        .live-search-item:hover {
            background: rgba(88, 207, 255, 0.18);
        }

        .live-search-thumb {
            width: 38px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
            background: rgba(88, 207, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
        }

        .live-search-meta {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .live-search-title {
            font-weight: 700;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .live-search-sub {
            font-size: 12px;
            color: #70dcff;
            margin-top: 2px;
        }

        body[data-theme="classic"] .live-search-sub {
            color: #0284c7;
        }

        .live-search-empty {
            padding: 14px 16px;
            text-align: center;
            font-size: 13px;
            color: #94a3b8;
        }

        .wii-search-button,
        .wii-filter-button,
        .wii-search-clear {
            padding: 12px 18px;
            border-radius: 30px;
            border: 1px solid #58cfff;
            background: rgba(25, 65, 85, 0.9);
            color: #70dcff;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            white-space: nowrap;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .wii-search-button:hover,
        .wii-filter-button:hover,
        .wii-search-clear:hover {
            background: #48bce8;
            color: #102330;
        }

        .wii-filter-button.has-active {
            background: #38bdf8;
            color: #0f172a;
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.45);
        }

        .filter-badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #0f172a;
            display: inline-block;
        }

        .wii-search-result {
            text-align: center;
            color: #a8d8ef;
            margin: 0 15px 20px;
            font-size: 14px;
        }

        body[data-theme="classic"] .wii-search-result {
            color: #0369a1;
        }

        .wii-no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 45px 20px;
            color: #b5d9eb;
        }

        .wii-no-results h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #62d8ff;
        }

        /* MODAL FILTERS & SORT ALA IDLIX */
        .idlix-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(6, 18, 28, 0.78);
            backdrop-filter: blur(6px);
            z-index: 1200;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .idlix-modal-backdrop.open {
            display: flex;
        }

        .idlix-modal-card {
            width: 100%;
            max-width: 660px;
            max-height: 88vh;
            overflow-y: auto;
            background: #0f2334;
            border: 1px solid rgba(88, 207, 255, 0.45);
            border-radius: 22px;
            padding: 24px 26px;
            color: #f1f5f9;
            box-shadow: 0 24px 55px rgba(0, 0, 0, 0.55);
            animation: modalPop 0.18s ease-out;
        }

        body[data-theme="classic"] .idlix-modal-card {
            background: #ffffff;
            color: #0f172a;
            border-color: rgba(14, 165, 233, 0.35);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.2);
        }

        @keyframes modalPop {
            from { opacity: 0; transform: scale(0.96) translateY(8px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .idlix-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .idlix-modal-header h2 {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
        }

        .idlix-modal-close {
            background: transparent;
            border: none;
            color: inherit;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
            opacity: 0.7;
            padding: 4px 8px;
            border-radius: 50%;
        }

        .idlix-modal-close:hover {
            opacity: 1;
            background: rgba(88, 207, 255, 0.15);
        }

        .idlix-section-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #7dd3fc;
            margin-bottom: 12px;
        }

        body[data-theme="classic"] .idlix-section-label {
            color: #0284c7;
        }

        .idlix-pill-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
        }

        .idlix-pill {
            padding: 9px 18px;
            border-radius: 999px;
            border: 1px solid rgba(88, 207, 255, 0.3);
            background: rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        body[data-theme="classic"] .idlix-pill {
            background: #f1f5f9;
            color: #334155;
            border-color: #cbd5e1;
        }

        .idlix-pill:hover {
            border-color: #38bdf8;
            transform: translateY(-1px);
        }

        .idlix-pill.active {
            background: #0ea5e9;
            border-color: #38bdf8;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(14, 165, 233, 0.4);
        }

        .idlix-genre-search {
            width: 100%;
            padding: 11px 16px;
            border-radius: 12px;
            border: 1px solid rgba(88, 207, 255, 0.35);
            background: rgba(10, 25, 38, 0.7);
            color: #f8fafc;
            font-size: 13.5px;
            margin-bottom: 14px;
            outline: none;
        }

        body[data-theme="classic"] .idlix-genre-search {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .idlix-genre-search:focus {
            border-color: #38bdf8;
        }

        .idlix-modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid rgba(88, 207, 255, 0.18);
            margin-top: 6px;
        }

        .idlix-btn-reset {
            padding: 10px 18px;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.4);
            background: transparent;
            color: inherit;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .idlix-btn-reset:hover {
            background: rgba(148, 163, 184, 0.15);
        }

        .idlix-btn-apply {
            padding: 10px 24px;
            border-radius: 999px;
            border: none;
            background: #0ea5e9;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(14, 165, 233, 0.35);
        }

        .idlix-btn-apply:hover {
            background: #0284c7;
        }

        @media (max-width: 600px) {
            .wii-search-form {
                flex-wrap: wrap;
            }

            .wii-search-container {
                flex-basis: 100%;
            }

            .wii-search-button,
            .wii-filter-button,
            .wii-search-clear {
                flex: 1;
                justify-content: center;
                text-align: center;
            }
        }

        /* WII MENU PAGINATION: tombol berada di luar sisi card */
        .wii-menu {
            position: relative;
            overflow: visible !important;
        }

        .channel-grid {
            position: relative;
            z-index: 1;
        }

        .page-arrows {
            position: absolute;
            top: 50%;
            left: 50% !important;
            right: auto !important;
            width: calc(100% + 112px);
            max-width: calc(100vw - 40px);
            box-sizing: border-box;
            transform: translate(-50%, -50%);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 20;
            pointer-events: none;
        }

        .page-arrow {
            width: 42px;
            height: 54px;
            flex: 0 0 42px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid rgba(88, 207, 255, 0.55);
            border-radius: 24px;
            background: rgba(18, 48, 65, 0.94);
            color: #58cfff;
            font-size: 34px;
            line-height: 1;
            text-decoration: none;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.2);
            transition: background 0.2s ease, transform 0.2s ease,
                        opacity 0.2s ease;
            pointer-events: auto;
        }

        .page-arrow:hover:not(.disabled) {
            background: #58cfff;
            color: #102330;
            transform: scale(1.08);
        }

        .page-arrow.disabled {
            opacity: 0.22;
            cursor: default;
        }

        @media (max-width: 600px) {
            .page-arrows {
                width: calc(100% + 12px);
                max-width: calc(100vw - 24px);
                top: auto;
                bottom: -58px;
                transform: translateX(-50%);
            }

            .page-arrow {
                width: 38px;
                height: 42px;
                flex-basis: 38px;
                font-size: 28px;
            }
        }
    </style>
</head>

<body class="wii-body" data-theme="classic">

<main class="wii-screen">

    {{-- TOP INFORMATION --}}
    <div class="wii-top">

        <span class="wii-brand">GAMEBACKLOG</span>

        <div class="wii-top-actions">

            {{-- Add Game: Admin Only --}}
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('games.create') }}"
                   class="wii-menu-button add-game">
                    + Add Game
                </a>
            @endif

            {{-- Refresh --}}
            <button type="button"
                    class="wii-menu-button refresh-game"
                    onclick="window.location.reload()">
                ↻ Refresh
            </button>

            {{-- Personal Library --}}
            <a href="{{ route('games.library') }}"
               class="wii-menu-button library-button">
                🎮 My Library
            </a>
            
            {{-- Export Excel --}}
            <a href="{{ route('games.export') }}"
               class="wii-menu-button export-button">
                📥 Export Excel
            </a>

            {{-- Profile --}}
            <a href="{{ url('/dashboard') }}"
               class="wii-menu-button profile-button">
                👤 Profile
            </a>

            <a href="{{ url('/rawg-catalog') }}" class="wii-api-button">
                🌐 Browse Game
            </a>

            {{-- Logout --}}
            <form action="{{ url('/logout') }}"
                  method="POST"
                  class="logout-form">
                @csrf

                <button type="submit"
                        class="wii-menu-button logout-button">
                    ↪ Logout
                </button>
            </form>

            {{-- Game Counter --}}
            <span class="wii-counter">
                {{ $games->total() }} Games
            </span>

            {{-- Theme Toggle --}}
            <button type="button"
                    class="theme-toggle"
                    id="wii-theme-toggle">
                🌙
            </button>

        </div>

    </div>


    {{-- SEARCH GAME & FILTER --}}
    @php
        $activeGenre = request('genre');
        $activeSort  = request('sort', 'latest');
        $hasActiveFilter = !empty($activeGenre) || ($activeSort !== 'latest');
    @endphp

    <div class="wii-search-wrapper">

        <form action="{{ route('games.index') }}"
              method="GET"
              class="wii-search-form"
              id="wii-search-form">

            <input type="hidden" name="genre" id="hidden-genre-input" value="{{ $activeGenre }}">
            <input type="hidden" name="sort" id="hidden-sort-input" value="{{ $activeSort }}">

            <div class="wii-search-container">
                <input
                    type="search"
                    name="search"
                    id="live-search-input"
                    class="wii-search-input"
                    placeholder="🔍 Search your favorite games..."
                    value="{{ request('search') }}"
                    autocomplete="off"
                >

                <!-- Kotak Saran Live Search -->
                <div id="live-search-dropdown" class="live-search-dropdown"></div>
            </div>

            <button type="submit"
                    class="wii-search-button">
                🔍 Search
            </button>

            <button type="button"
                    id="open-filter-modal"
                    class="wii-filter-button {{ $hasActiveFilter ? 'has-active' : '' }}">
                🎛️ Filter
                @if ($hasActiveFilter)
                    <span class="filter-badge-dot"></span>
                @endif
            </button>

            @if (request('search') || $hasActiveFilter)
                <a href="{{ route('games.index') }}"
                   class="wii-search-clear">
                    ✕ Reset
                </a>
            @endif

        </form>

    </div>


    {{-- SEARCH & FILTER RESULT INFORMATION --}}
    @if (request('search') || $hasActiveFilter)
        @php
            $sortLabels = [
                'latest'    => 'Latest Added',
                'newest'    => 'Newest Release',
                'popular'   => 'Most Popular',
                'top_rated' => 'Top Rated',
            ];
            $selectedGenreObj = isset($genres) ? $genres->firstWhere('id', $activeGenre) : null;
        @endphp

        <div class="wii-search-result">
            Menampilkan <strong>{{ $games->total() }}</strong> game
            @if (request('search'))
                untuk <strong>"{{ request('search') }}"</strong>
            @endif
            @if ($selectedGenreObj)
                &bull; Genre: <strong>{{ $selectedGenreObj->name }}</strong>
            @endif
            @if ($activeSort !== 'latest')
                &bull; Urutan: <strong>{{ $sortLabels[$activeSort] ?? 'Latest' }}</strong>
            @endif
        </div>
    @endif


    {{-- MODAL FILTERS & SORT ALA IDLIX --}}
    <div id="idlix-filter-modal" class="idlix-modal-backdrop" aria-hidden="true">
        <div class="idlix-modal-card" role="dialog" aria-modal="true" aria-labelledby="filter-modal-title">
            
            <div class="idlix-modal-header">
                <h2 id="filter-modal-title">Filters &amp; Sort</h2>
                <button type="button" id="close-filter-modal" class="idlix-modal-close" aria-label="Tutup">&times;</button>
            </div>

            {{-- SORT BY --}}
            <div class="idlix-section-label">
                <span>⇅</span> SORT BY
            </div>
            <div class="idlix-pill-group" id="sort-pills-group">
                <button type="button" class="idlix-pill {{ $activeSort === 'latest' ? 'active' : '' }}" data-sort="latest">
                    Latest
                </button>
                <button type="button" class="idlix-pill {{ $activeSort === 'newest' ? 'active' : '' }}" data-sort="newest">
                    Newest Release
                </button>
                <button type="button" class="idlix-pill {{ $activeSort === 'popular' ? 'active' : '' }}" data-sort="popular">
                    Popular
                </button>
                <button type="button" class="idlix-pill {{ $activeSort === 'top_rated' ? 'active' : '' }}" data-sort="top_rated">
                    Top Rated
                </button>
            </div>

            {{-- GENRES --}}
            <div class="idlix-section-label">
                <span>🏷️</span> GENRES
            </div>
            <input type="text"
                   id="genre-filter-search"
                   class="idlix-genre-search"
                   placeholder="🔍 Search genres...">

            <div class="idlix-pill-group" id="genre-pills-group">
                <button type="button"
                        class="idlix-pill genre-pill {{ empty($activeGenre) ? 'active' : '' }}"
                        data-genre=""
                        data-genre-name="all genres">
                    All Genres
                </button>

                @if (isset($genres))
                    @foreach ($genres as $genre)
                        <button type="button"
                                class="idlix-pill genre-pill {{ (string) $activeGenre === (string) $genre->id ? 'active' : '' }}"
                                data-genre="{{ $genre->id }}"
                                data-genre-name="{{ strtolower($genre->name) }}">
                            {{ $genre->name }}
                        </button>
                    @endforeach
                @endif
            </div>

            <div class="idlix-modal-footer">
                <a href="{{ route('games.index') }}" class="idlix-btn-reset">Reset All</a>
                <button type="button" id="apply-filter-btn" class="idlix-btn-apply">Apply Filters</button>
            </div>

        </div>
    </div>


    {{-- CHANNEL MENU --}}
    <section class="wii-menu">

        <div class="channel-grid">

            @forelse ($games as $game)

                {{-- GAME CHANNEL --}}
                <div class="channel"

                    @if (auth()->user()->role === 'admin')
                        data-delete-url="{{ route('games.destroy', $game->id) }}"
                    @endif

                    data-show-url="{{ route('games.show', $game->id) }}">

                    {{-- EDIT BUTTON: ADMIN ONLY --}}
                    @if (auth()->user()->role === 'admin')

                        <button type="button"
                                class="wii-edit-button"
                                data-edit-url="{{ route('games.edit', $game->id) }}"
                                title="Edit {{ $game->title }}">
                            ✎
                        </button>

                    @endif


                    {{-- GAME PREVIEW --}}
                    <div class="channel-preview">

                        @if ($game->cover_image)

                            @php
                                $coverUrl = \Illuminate\Support\Str::startsWith(
                                    $game->cover_image,
                                    ['http://', 'https://']
                                )
                                    ? $game->cover_image
                                    : asset('storage/' . $game->cover_image);
                            @endphp

                            <img
                                src="{{ $coverUrl }}"
                                alt="{{ $game->title }}"
                                loading="lazy"
                                onerror="this.style.display='none'"
                            >

                        @else

                            <div class="empty-channel">
                                <span>🎮</span>
                            </div>

                        @endif

                        <div class="channel-gloss"></div>

                    </div>


                    {{-- GAME TITLE --}}
                    <div class="channel-label">
                        {{ $game->title }}
                    </div>

                </div>

            @empty

                {{-- NO SEARCH RESULTS --}}
                <div class="wii-no-results">

                    <h3>🎮 Game Not Found</h3>

                    <p>
                        Maaf, game yang kamu cari tidak ditemukan.
                    </p>

                    <p>
                        Coba gunakan judul atau filter lainnya.
                    </p>

                </div>

            @endforelse


            {{-- EMPTY CHANNELS --}}
            @if ($games->count() > 0)

                @for ($i = $games->count(); $i < 12; $i++)

                    <div class="channel empty-slot">
                        <div class="empty-channel"></div>
                    </div>

                @endfor

            @endif

        </div>


        {{-- PAGE NAVIGATION --}}
        <div class="page-arrows">

            @if ($games->appends(request()->query())->previousPageUrl())

                <a href="{{ $games->appends(request()->query())->previousPageUrl() }}"
                   class="page-arrow previous">
                    ‹
                </a>

            @else

                <span class="page-arrow disabled">
                    ‹
                </span>

            @endif


            @if ($games->appends(request()->query())->nextPageUrl())

                <a href="{{ $games->appends(request()->query())->nextPageUrl() }}"
                   class="page-arrow next">
                    ›
                </a>

            @else

                <span class="page-arrow disabled">
                    ›
                </span>

            @endif

        </div>

    </section>

</main>


{{-- WII BOTTOM DOCK --}}
<footer class="wii-dock">

    <div class="dock-left">
        <div class="wii-circle">Wii</div>
    </div>

    <div class="dock-center">

        <div class="wii-time">
            {{ now()->format('H:i') }}
            <small>{{ now()->format('A') }}</small>
        </div>

        <div class="wii-date">
            {{ now()->format('D d/m') }}
        </div>

    </div>

    <div class="dock-right">

        <div class="mail-circle">
            ✉
        </div>

    </div>

</footer>


{{-- WII CHANNEL OPENING SYSTEM, LIVE SEARCH & FILTER MODAL --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    // 1. WII THEME SWITCHER
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

    if (themeToggle) {
        applyTheme(savedTheme);

        themeToggle.addEventListener('click', function () {
            const currentTheme = document.body.dataset.theme;
            const newTheme = currentTheme === 'classic' ? 'midnight' : 'classic';
            applyTheme(newTheme);
        });
    }


    // 2. EDIT GAME BUTTON
    document.querySelectorAll('.wii-edit-button')
        .forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();

                const editUrl = this.dataset.editUrl;
                if (editUrl) {
                    window.location.href = editUrl;
                }
            });
        });


    // 3. CREATE WII OPENING OVERLAY
    const overlay = document.createElement('div');
    overlay.className = 'wii-opening-overlay';

    overlay.innerHTML = `
        <div class="wii-opening-panel">
            <div class="wii-opening-backdrop"></div>
            <div class="wii-opening-header">GAMEBACKLOG</div>
            <div class="wii-opening-cover"></div>
            <h2 class="wii-opening-title"></h2>
            <div class="wii-opening-actions">
                <button class="wii-action-button" id="wii-back">Back</button>
                <button class="wii-action-button primary" id="wii-start">View Details</button>

                @if (auth()->user()->role === 'admin')
                    <form id="wii-delete-form" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="wii-action-button" id="wii-delete">Delete Game</button>
                    </form>
                @endif
            </div>
        </div>
    `;

    document.body.appendChild(overlay);


    // 4. OVERLAY ELEMENTS
    const titleElement = overlay.querySelector('.wii-opening-title');
    const coverElement = overlay.querySelector('.wii-opening-cover');
    const backdropElement = overlay.querySelector('.wii-opening-backdrop');
    let selectedGame = null;
    const deleteForm = document.getElementById('wii-delete-form');


    // 5. OPEN GAME CHANNEL
    document.addEventListener('click', function(event) {
        const channel = event.target.closest('.channel');
        if (!channel) return;
        if (channel.classList.contains('empty-slot')) return;

        const title = channel.querySelector('.channel-label');
        if (!title) return;

        event.preventDefault();
        selectedGame = channel;

        if (deleteForm) {
            deleteForm.action = channel.dataset.deleteUrl || '';
        }

        titleElement.textContent = title.textContent.trim();

        const image = channel.querySelector('img');
        if (image && image.src && image.style.display !== 'none') {
            const coverUrl = `url("${image.src}")`;
            coverElement.style.backgroundImage = coverUrl;
            backdropElement.style.backgroundImage = coverUrl;
        } else {
            coverElement.style.backgroundImage = '';
            backdropElement.style.backgroundImage = '';
        }

        overlay.classList.add('active');
    });


    // 6. BACK BUTTON
    document.getElementById('wii-back').addEventListener('click', function() {
        overlay.classList.remove('active');
        selectedGame = null;
    });


    // 7. VIEW DETAILS BUTTON
    document.getElementById('wii-start').addEventListener('click', function() {
        if (!selectedGame) return;
        const detailUrl = selectedGame.dataset.showUrl;
        if (detailUrl) {
            window.location.href = detailUrl;
        }
    });


    // 8. CLICK OUTSIDE OVERLAY
    overlay.addEventListener('click', function(event) {
        if (event.target === overlay) {
            overlay.classList.remove('active');
            selectedGame = null;
        }
    });


    // 9. ESCAPE KEY
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            overlay.classList.remove('active');
            selectedGame = null;
            const filterModal = document.getElementById('idlix-filter-modal');
            if (filterModal) filterModal.classList.remove('open');
        }
    });


    // 10. DELETE GAME CONFIRMATION
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(event) {
            const confirmed = confirm('Apakah kamu yakin ingin menghapus game ini?');
            if (!confirmed) {
                event.preventDefault();
            }
        });
    }


    // 11. LIVE SEARCH AUTOCOMPLETE
    const searchInput = document.getElementById('live-search-input');
    const searchDropdown = document.getElementById('live-search-dropdown');
    let debounceTimer = null;

    if (searchInput && searchDropdown) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 1) {
                searchDropdown.style.display = 'none';
                searchDropdown.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(async () => {
                try {
                    const response = await fetch(`{{ route('games.liveSearch') }}?q=${encodeURIComponent(query)}`);
                    const games = await response.json();

                    if (games.length === 0) {
                        searchDropdown.innerHTML = `
                            <div class="live-search-empty">
                                Tidak ada game yang cocok dengan "<strong>${query}</strong>"
                            </div>
                        `;
                    } else {
                        searchDropdown.innerHTML = games.map(game => `
                            <a href="${game.url}" class="live-search-item">
                                ${game.cover
                                    ? `<img src="${game.cover}" alt="${game.title}" class="live-search-thumb">`
                                    : `<div class="live-search-thumb">🎮</div>`
                                }
                                <div class="live-search-meta">
                                    <span class="live-search-title">${game.title}</span>
                                    <span class="live-search-sub">${game.genre} &bull; ${game.release_year}</span>
                                </div>
                            </a>
                        `).join('');
                    }

                    searchDropdown.style.display = 'block';
                } catch (err) {
                    console.error('Live search error:', err);
                }
            }, 250);
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.wii-search-container')) {
                searchDropdown.style.display = 'none';
            }
        });

        searchInput.addEventListener('focus', function () {
            if (this.value.trim().length > 0 && searchDropdown.innerHTML.trim() !== '') {
                searchDropdown.style.display = 'block';
            }
        });
    }


    // 12. MODAL FILTERS & SORT ALA IDLIX
    const filterModal = document.getElementById('idlix-filter-modal');
    const openFilterBtn = document.getElementById('open-filter-modal');
    const closeFilterBtn = document.getElementById('close-filter-modal');
    const applyFilterBtn = document.getElementById('apply-filter-btn');
    const hiddenGenreInput = document.getElementById('hidden-genre-input');
    const hiddenSortInput = document.getElementById('hidden-sort-input');
    const mainSearchForm = document.getElementById('wii-search-form');
    const genreSearchInput = document.getElementById('genre-filter-search');

    if (openFilterBtn && filterModal) {
        openFilterBtn.addEventListener('click', () => {
            filterModal.classList.add('open');
        });
    }

    if (closeFilterBtn && filterModal) {
        closeFilterBtn.addEventListener('click', () => {
            filterModal.classList.remove('open');
        });
    }

    if (filterModal) {
        filterModal.addEventListener('click', (e) => {
            if (e.target === filterModal) {
                filterModal.classList.remove('open');
            }
        });
    }

    // Pilihan pill Sort By
    document.querySelectorAll('#sort-pills-group .idlix-pill').forEach(pill => {
        pill.addEventListener('click', () => {
            document.querySelectorAll('#sort-pills-group .idlix-pill').forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            hiddenSortInput.value = pill.dataset.sort;
        });
    });

    // Pilihan pill Genre
    document.querySelectorAll('#genre-pills-group .genre-pill').forEach(pill => {
        pill.addEventListener('click', () => {
            document.querySelectorAll('#genre-pills-group .genre-pill').forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            hiddenGenreInput.value = pill.dataset.genre;
        });
    });

    // Live filter daftar genre di dalam modal
    if (genreSearchInput) {
        genreSearchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            document.querySelectorAll('#genre-pills-group .genre-pill').forEach(pill => {
                const name = pill.dataset.genreName || '';
                if (pill.dataset.genre === '' || name.includes(term)) {
                    pill.style.display = 'inline-block';
                } else {
                    pill.style.display = 'none';
                }
            });
        });
    }

    // Tombol Apply Filters -> submit form pencarian utama
    if (applyFilterBtn && mainSearchForm) {
        applyFilterBtn.addEventListener('click', () => {
            mainSearchForm.submit();
        });
    }

});

</script>

</body>
</html>