
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GameBacklog - Menu</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* SEARCH BAR */
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
            max-width: 650px;
        }

        .wii-search-input {
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

        .wii-search-button,
        .wii-search-clear {
            padding: 12px 20px;
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
        }

        .wii-search-button:hover,
        .wii-search-clear:hover {
            background: #48bce8;
            color: #102330;
        }

        .wii-search-result {
            text-align: center;
            color: #a8d8ef;
            margin: 0 15px 20px;
            font-size: 14px;
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

        @media (max-width: 600px) {
            .wii-search-form {
                flex-wrap: wrap;
            }

            .wii-search-input {
                flex-basis: 100%;
            }

            .wii-search-button,
            .wii-search-clear {
                flex: 1;
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

{{-- Add Game, Export & Import: Admin Only --}}
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('games.create') }}"
                   class="wii-menu-button add-game">
                    + Add Game
                </a>

                <a href="{{ route('games.export') }}"
                   class="wii-menu-button export-button">
                    📥 Export Excel
                </a>

                <button type="button"
                        class="wii-menu-button import-button"
                        onclick="document.getElementById('wii-import-modal').classList.add('active')">
                    📤 Import Excel
                </button>
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


    {{-- SEARCH GAME --}}
    <div class="wii-search-wrapper">

        <form action="{{ route('games.index') }}"
              method="GET"
              class="wii-search-form">

            <input
                type="search"
                name="search"
                class="wii-search-input"
                placeholder="🔍 Search your favorite games..."
                value="{{ request('search') }}"
                autocomplete="off"
            >

            <button type="submit"
                    class="wii-search-button">
                🔍 Search
            </button>

            @if (request('search'))
                <a href="{{ route('games.index') }}"
                   class="wii-search-clear">
                    ✕ Clear
                </a>
            @endif

        </form>

    </div>


    {{-- SEARCH RESULT INFORMATION --}}
    @if (request('search'))

        <div class="wii-search-result">

            @if ($games->total() > 0)

                Menampilkan {{ $games->total() }}
                hasil untuk
                <strong>"{{ request('search') }}"</strong>

            @else

                Tidak ditemukan game dengan judul
                <strong>"{{ request('search') }}"</strong>

            @endif

        </div>

    @endif


    {{-- CHANNEL MENU --}}
    @if (session('success'))
        <div class="wii-search-result" style="color: #65ff9f; font-weight: bold;">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('file'))
        <div class="wii-search-result" style="color: #ff7b7b; font-weight: bold;">
            ✕ {{ $errors->first('file') }}
        </div>
    @endif

    @if (auth()->user()->role === 'admin')
        <div class="wii-opening-overlay" id="wii-import-modal"
             onclick="if(event.target === this) this.classList.remove('active')">
            <div class="wii-opening-panel" style="padding: 28px; max-width: 460px;">
                <div class="wii-opening-header">IMPORT KATALOG (ADMIN)</div>
                <h2 class="wii-opening-title" style="margin-bottom: 10px;">Upload Katalog Game</h2>
                <p style="font-size: 13px; color: #a8d8ef; margin-bottom: 18px; line-height: 1.5;">
                    Kolom: <code>Judul Game</code>, <code>Genre</code>, <code>Platform</code>, <code>Developer</code>, <code>Publisher</code>, <code>Tanggal Rilis</code>, <code>Deskripsi</code>.
                </p>
                <form action="{{ route('games.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="wii-search-input" style="width: 100%; margin-bottom: 20px; border-radius: 14px;">
                    <div class="wii-opening-actions">
                        <button type="button" class="wii-action-button" onclick="document.getElementById('wii-import-modal').classList.remove('active')">Cancel</button>
                        <button type="submit" class="wii-action-button primary">📤 Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
                        Coba gunakan judul game lainnya.
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


{{-- WII CHANNEL OPENING SYSTEM --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    // =================================
    // 1. WII THEME SWITCHER
    // =================================

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


    if (themeToggle) {

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

    }


    // =================================
    // 2. EDIT GAME BUTTON
    // =================================

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


    // =================================
    // 3. CREATE WII OPENING OVERLAY
    // =================================

    const overlay = document.createElement('div');

    overlay.className = 'wii-opening-overlay';

    overlay.innerHTML = `

        <div class="wii-opening-panel">

            <div class="wii-opening-backdrop"></div>

            <div class="wii-opening-header">
                GAMEBACKLOG
            </div>

            <div class="wii-opening-cover"></div>

            <h2 class="wii-opening-title"></h2>

            <div class="wii-opening-actions">

                <button class="wii-action-button"
                        id="wii-back">
                    Back
                </button>

                <button class="wii-action-button primary"
                        id="wii-start">
                    View Details
                </button>

                @if (auth()->user()->role === 'admin')

                    <form id="wii-delete-form"
                          method="POST"
                          action="">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="wii-action-button"
                                id="wii-delete">
                            Delete Game
                        </button>

                    </form>

                @endif

            </div>

        </div>

    `;

    document.body.appendChild(overlay);


    // =================================
    // 4. OVERLAY ELEMENTS
    // =================================

    const titleElement =
        overlay.querySelector('.wii-opening-title');

    const coverElement =
        overlay.querySelector('.wii-opening-cover');

    const backdropElement =
        overlay.querySelector('.wii-opening-backdrop');

    let selectedGame = null;

    const deleteForm =
        document.getElementById('wii-delete-form');


    // =================================
    // 5. OPEN GAME CHANNEL
    // =================================

    document.addEventListener('click', function(event) {

        const channel =
            event.target.closest('.channel');

        if (!channel) return;

        if (channel.classList.contains('empty-slot')) {
            return;
        }

        const title =
            channel.querySelector('.channel-label');

        if (!title) return;

        event.preventDefault();

        selectedGame = channel;

        // Set Delete URL untuk Admin
        if (deleteForm) {

            deleteForm.action =
                channel.dataset.deleteUrl || '';

        }

        // Set judul game
        titleElement.textContent =
            title.textContent.trim();

        // Ambil cover game
        const image =
            channel.querySelector('img');

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


    // =================================
    // 6. BACK BUTTON
    // =================================

    document.getElementById('wii-back')
        .addEventListener('click', function() {

            overlay.classList.remove('active');

            selectedGame = null;

        });


    // =================================
    // 7. VIEW DETAILS BUTTON
    // =================================

    document.getElementById('wii-start')
        .addEventListener('click', function() {

            if (!selectedGame) return;

            const detailUrl =
                selectedGame.dataset.showUrl;

            if (detailUrl) {

                window.location.href = detailUrl;

            }

        });


    // =================================
    // 8. CLICK OUTSIDE OVERLAY
    // =================================

    overlay.addEventListener('click', function(event) {

        if (event.target === overlay) {

            overlay.classList.remove('active');

            selectedGame = null;

        }

    });


    // =================================
    // 9. ESCAPE KEY
    // =================================

    document.addEventListener('keydown', function(event) {

        if (event.key === 'Escape') {

            overlay.classList.remove('active');

            selectedGame = null;

        }

    });

    // =================================
    // 10. DELETE GAME CONFIRMATION
    // =================================

    if (deleteForm) {

        deleteForm.addEventListener('submit', function(event) {

            const confirmed = confirm(
                'Apakah kamu yakin ingin menghapus game ini?'
            );

            if (!confirmed) {

                event.preventDefault();

            }
            
        });

    }

    // =================================
    // 11. WII REAL-TIME LOCAL CLOCK
    // =================================

    const timeEl = document.querySelector('.wii-time');
    const dateEl = document.querySelector('.wii-date');

    function updateWiiClock() {
        const now = new Date(); // Otomatis membaca jam & zona waktu lokasi perangkat user

        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';

        hours = hours % 12;
        hours = hours ? hours : 12; // Jam 0 menjadi 12
        const formattedHours = String(hours).padStart(2, '0');

        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const dayName = days[now.getDay()];
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');

        if (timeEl) {
            timeEl.innerHTML = `${formattedHours}:${minutes}\n            <small>${ampm}</small>`;
        }

        if (dateEl) {
            dateEl.textContent = `${dayName} ${day}/${month}`;
        }
    }

    updateWiiClock();
    setInterval(updateWiiClock, 1000);

});

</script>

</body>
</html>
