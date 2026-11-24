<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Library - {{ $userGame->game->title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="wii-body" data-theme="classic">

<div class="library-edit-page">

    <div class="library-edit-top">
        <a href="{{ route('games.library') }}" class="wii-back-button">
            ← My Library
        </a>

        <button type="button"
                class="theme-toggle"
                id="wii-theme-toggle">
            🌙
        </button>
    </div>

    <div class="library-edit-container">

        <div class="library-edit-heading">
            <div>🎮</div>
            <h1>Edit My Game</h1>
            <p>Atur progres bermain dan informasi koleksi pribadimu.</p>
        </div>

        <div class="library-edit-game">

            @php
                $coverImage = trim((string) ($userGame->game->cover_image ?? ''));

                if ($coverImage === '') {
                    $coverUrl = null;
                } elseif (filter_var($coverImage, FILTER_VALIDATE_URL)) {
                    // Gambar eksternal dari RAWG
                    $coverUrl = $coverImage;
                } elseif (str_starts_with($coverImage, '/storage/')) {
                    // Path yang sudah memiliki prefix storage
                    $coverUrl = asset(ltrim($coverImage, '/'));
                } elseif (str_starts_with($coverImage, 'storage/')) {
                    // Path storage tanpa slash di depan
                    $coverUrl = asset($coverImage);
                } else {
                    // Path gambar lokal Laravel
                    $coverUrl = asset('storage/' . ltrim($coverImage, '/'));
                }
            @endphp

            @if ($coverUrl)
                <img
                    src="{{ $coverUrl }}"
                    alt="Cover {{ $userGame->game->title }}"
                    onerror="this.style.display='none';">
            @else
                <div class="no-cover-placeholder">
                    🎮
                    <span>Cover tidak tersedia</span>
                </div>
            @endif

            <h2>{{ $userGame->game->title }}</h2>
        </div>

        @if ($errors->any())
            <div class="library-edit-errors">
                <strong>Periksa kembali input berikut:</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('games.library.update', $userGame->id) }}"
              method="POST"
              class="library-edit-form">

            @csrf
            @method('PUT')

            <div class="library-edit-field">
                <label for="status">Status Game</label>

                <select name="status" id="status" required>
                    @foreach ([
                        'Plan to Play',
                        'Backlog',
                        'Playing',
                        'Completed',
                        'Dropped'
                    ] as $status)
                        <option value="{{ $status }}"
                            {{ old('status', $userGame->status) == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="library-edit-field">
                <label for="platform_id">Platform yang Digunakan</label>

                <select name="platform_id" id="platform_id">
                    <option value="">Belum dipilih</option>

                    @foreach ($platforms as $platform)
                        <option value="{{ $platform->id }}"
                            {{ old('platform_id', $userGame->platform_id) == $platform->id ? 'selected' : '' }}>
                            {{ $platform->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="library-edit-field">
                <label for="playtime">Playtime (Hours)</label>

                <input
                    type="number"
                    name="playtime"
                    id="playtime"
                    min="0"
                    value="{{ old('playtime', $userGame->playtime) }}"
                    required>
            </div>

            <div class="library-edit-field">
                <label for="priority">Priority</label>

                <select name="priority" id="priority">
                    <option value="">Tidak ditentukan</option>

                    @foreach (['Low', 'Medium', 'High'] as $priority)
                        <option value="{{ $priority }}"
                            {{ old('priority', $userGame->priority) == $priority ? 'selected' : '' }}>
                            {{ $priority }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="library-edit-actions">
                <a href="{{ route('games.library') }}"
                   class="wii-cancel-button">
                    Cancel
                </a>

                <button type="submit" class="wii-edit-submit">
                    Save Changes
                </button>
            </div>

        </form>

    </div>

    <div class="library-edit-footer">
        GAMEBACKLOG · PERSONAL COLLECTION
    </div>

</div>

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

</body>
</html>
