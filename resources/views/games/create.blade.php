<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Game - GameBacklog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="wii-body" data-theme="classic">

<div class="wii-form-page">

    <div class="wii-form-top">
        <a href="{{ route('games.index') }}" class="wii-back-button">
            ← Menu
        </a>

        <button type="button"
                class="theme-toggle"
                id="wii-theme-toggle"
                aria-label="Ganti tema"
                title="Ganti tema">
            🌙
        </button>
    </div>

    <div class="wii-form-container">

        <div class="wii-form-heading">
            <div class="wii-form-icon">🎮</div>
            <h1>Add a New Game</h1>
            <p>Tambahkan game baru ke koleksi GameBacklog kamu.</p>
        </div>

        @if ($errors->any())
            <div class="wii-form-errors">
                <strong>Terjadi kesalahan!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('games.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="wii-game-form">

            @csrf

            <div class="wii-form-section">
                <h3>Game Information</h3>

                <div class="wii-field">
                    <label for="title">Judul Game</label>
                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title') }}"
                           placeholder="Masukkan judul game"
                           required>
                </div>

                <div class="wii-field">
                    <label for="genre_id">Genre</label>
                    <select id="genre_id" name="genre_id" required>
                        <option value="">Pilih Genre</option>
                        @foreach ($genres as $genre)
                            <option value="{{ $genre->id }}"
                                {{ old('genre_id') == $genre->id ? 'selected' : '' }}>
                                {{ $genre->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="wii-field-row">
                    <div class="wii-field">
                        <label for="developer">Developer</label>
                        <input type="text"
                               id="developer"
                               name="developer"
                               value="{{ old('developer') }}"
                               placeholder="Developer"
                               required>
                    </div>

                    <div class="wii-field">
                        <label for="publisher">Publisher</label>
                        <input type="text"
                               id="publisher"
                               name="publisher"
                               value="{{ old('publisher') }}"
                               placeholder="Publisher"
                               required>
                    </div>
                </div>

                <div class="wii-field">
                    <label for="release_date">Tanggal Rilis</label>
                    <input type="date"
                           id="release_date"
                           name="release_date"
                           value="{{ old('release_date') }}"
                           required>
                </div>
            </div>

            <div class="wii-form-section">
                <h3>Available Platforms</h3>
                <p class="wii-section-description">
                    Pilih platform tempat game ini tersedia.
                </p>

                <div class="wii-platform-grid">
                    @foreach ($platforms as $platform)
                        <label class="wii-platform-option">
                            <input type="checkbox"
                                   name="platforms[]"
                                   value="{{ $platform->id }}"
                                   {{ in_array($platform->id, old('platforms', [])) ? 'checked' : '' }}>
                            <span>{{ $platform->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="wii-form-section">
                <h3>Additional Information</h3>

                <div class="wii-field">
                    <label for="description">Deskripsi</label>
                    <textarea id="description"
                              name="description"
                              rows="4"
                              placeholder="Ceritakan sedikit tentang game ini...">{{ old('description') }}</textarea>
                </div>

                <div class="wii-field">
                    <label for="cover_image">Cover Image</label>
                    <input type="file"
                           id="cover_image"
                           name="cover_image"
                           accept="image/*">

                    <small>Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</small>
                </div>
            </div>

            <div class="wii-form-actions">
                <a href="{{ route('games.index') }}" class="wii-cancel-button">
                    Cancel
                </a>

                <button type="submit" class="wii-submit-button">
                    ✓ Save Game
                </button>
            </div>

        </form>
    </div>

    <div class="wii-form-footer">
        GAMEBACKLOG · WII COLLECTION SYSTEM
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('wii-theme-toggle');

    if (!themeToggle) return;

    const savedTheme = localStorage.getItem('wii-theme') || 'classic';

    function applyTheme(theme) {
        document.body.dataset.theme = theme;
        localStorage.setItem('wii-theme', theme);

        if (theme === 'midnight') {
            themeToggle.textContent = '☀️';
            themeToggle.title = 'Aktifkan Wii Classic';
            themeToggle.setAttribute('aria-label', 'Aktifkan Wii Classic');
        } else {
            themeToggle.textContent = '🌙';
            themeToggle.title = 'Aktifkan Wii Midnight';
            themeToggle.setAttribute('aria-label', 'Aktifkan Wii Midnight');
        }
    }

    applyTheme(savedTheme);

    themeToggle.addEventListener('click', function () {
        const currentTheme = document.body.dataset.theme;
        const newTheme = currentTheme === 'classic'
            ? 'midnight'
            : 'classic';

        applyTheme(newTheme);
    });
});
</script>

</body>
</html>