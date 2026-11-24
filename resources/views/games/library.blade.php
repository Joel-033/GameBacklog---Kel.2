
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

                        // Deteksi jenis gambar
                        if ($coverImage === '') {
                            $coverUrl = null;
                        } elseif (filter_var($coverImage, FILTER_VALIDATE_URL)) {
                            // Gambar dari RAWG API (URL eksternal)
                            $coverUrl = $coverImage;
                        } elseif (str_starts_with($coverImage, '/storage/')) {
                            // Path yang sudah mengandung /storage/
                            $coverUrl = asset(ltrim($coverImage, '/'));
                        } elseif (str_starts_with($coverImage, 'storage/')) {
                            // Path yang sudah mengandung storage/
                            $coverUrl = asset($coverImage);
                        } else {
                            // Gambar lokal dari Laravel Storage
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

        applyTheme(
            currentTheme === 'classic' ? 'midnight' : 'classic'
        );

    });

});
</script>

</body>
</html>
