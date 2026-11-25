<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RawgController;
use App\Models\Game;
use App\Models\Genre;


// =========================================
// HALAMAN UTAMA
// =========================================

Route::get('/', function () {
    return redirect('/login');
});


// =========================================
// GUEST: PENGGUNA BELUM LOGIN
// =========================================

Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);

});


// =========================================
// AUTHENTICATED: SEMUA PENGGUNA LOGIN
// =========================================

Route::middleware('auth')->group(function () {

    // Dashboard (dengan Analitik Khusus Admin)
    Route::get('/dashboard', function () {
        $adminAnalytics = null;

        if (auth()->user()->role === 'admin') {
            // 1. Data Bar Chart: Distribusi Game per Genre
            $genres = Genre::withCount('games')
                ->orderByDesc('games_count')
                ->orderBy('name')
                ->get();

            $genreLabels = $genres->pluck('name')->values();
            $genreCounts = $genres->pluck('games_count')->values();

            // Helper untuk mengambil Top 5 game berdasarkan rentang waktu di tabel user_games
            $getTopGames = function ($startDate) {
                return Game::with('genre')
                    ->withCount(['userGames as popularity_count' => function ($query) use ($startDate) {
                        $query->where('created_at', '>=', $startDate);
                    }])
                    ->having('popularity_count', '>', 0)
                    ->orderByDesc('popularity_count')
                    ->orderBy('title')
                    ->take(5)
                    ->get()
                    ->map(function ($game, $index) {
                        return [
                            'rank'  => $index + 1,
                            'title' => $game->title,
                            'genre' => $game->genre->name ?? 'Uncategorized',
                            'count' => $game->popularity_count,
                        ];
                    })
                    ->values();
            };

            $adminAnalytics = [
                'genreLabels' => $genreLabels,
                'genreCounts' => $genreCounts,
                'topDay'      => $getTopGames(now()->startOfDay()),
                'topWeek'     => $getTopGames(now()->subDays(7)),
                'topMonth'    => $getTopGames(now()->subDays(30)),
            ];
        }

        return view('dashboard', compact('adminAnalytics'));
    })->name('dashboard');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


    // =====================================
    // KATALOG GAME
    // =====================================

    // Menampilkan katalog game
    Route::get('/games', [GameController::class, 'index'])
        ->name('games.index');

    // Live Search Autocomplete (JSON)
    Route::get('/games/live-search', [GameController::class, 'liveSearch'])
        ->name('games.liveSearch');

    // Export katalog game ke Excel
    Route::get('/games/export', [GameController::class, 'exportExcel'])
        ->name('games.export');


    // =====================================
    // KELOLA KATALOG: ADMIN SAJA
    // =====================================

    Route::middleware('admin')->group(function () {

        // Menampilkan form tambah game
        Route::get('/games/create', [GameController::class, 'create'])
            ->name('games.create');

        // Menyimpan game baru
        Route::post('/games', [GameController::class, 'store'])
            ->name('games.store');

        // Menampilkan form edit game
        Route::get('/games/{id}/edit', [GameController::class, 'edit'])
            ->whereNumber('id')
            ->name('games.edit');

        // Memperbarui data game
        Route::put('/games/{id}', [GameController::class, 'update'])
            ->whereNumber('id')
            ->name('games.update');

        // Menghapus game
        Route::delete('/games/{id}', [GameController::class, 'destroy'])
            ->whereNumber('id')
            ->name('games.destroy');

    });


    // =====================================
    // DETAIL GAME: USER DAN ADMIN
    // =====================================

    // Letakkan setelah route /games/create
    Route::get('/games/{id}', [GameController::class, 'show'])
        ->whereNumber('id')
        ->name('games.show');


    // =====================================
    // PERSONAL LIBRARY: USER DAN ADMIN
    // =====================================

    // Menambahkan game ke Personal Library
    Route::post('/games/{id}/library', [GameController::class, 'addToLibrary'])
        ->whereNumber('id')
        ->name('games.library.add');

    // Menampilkan Personal Library
    Route::get('/my-library', [GameController::class, 'myLibrary'])
        ->name('games.library');

    // Menampilkan form edit Personal Library
    Route::get('/my-library/{id}/edit', [GameController::class, 'editLibrary'])
        ->whereNumber('id')
            ->name('games.library.edit');

    // Memperbarui Personal Library
    Route::put('/my-library/{id}', [GameController::class, 'updateLibrary'])
        ->whereNumber('id')
        ->name('games.library.update');

    // Menghapus game dari Personal Library
    Route::delete('/my-library/{id}', [GameController::class, 'removeFromLibrary'])
        ->whereNumber('id')
        ->name('games.library.remove');

    Route::post('/rawg-game/{id}/add',
    [RawgController::class, 'addToMyGames']
    )->name('rawg.add');

});

Route::get('/test-rawg', [RawgController::class, 'search']);

Route::get('/rawg-catalog', [RawgController::class, 'catalog']);

Route::get('/rawg-game/{id}', [RawgController::class, 'detail'])
    ->name('rawg.detail');

Route::post('/rawg-game/{id}/add', [
    RawgController::class,
    'addToMyGames'
])
->middleware('auth')
->name('rawg.add');

Route::post('/rawg-game/{id}/add', [RawgController::class, 'addToMyGames'])
    ->middleware('auth')
    ->name('rawg.add');