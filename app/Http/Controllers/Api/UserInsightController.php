<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Game;
use App\Models\Review;
use App\Models\User;
use App\Models\UserGame;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UserInsightController extends Controller
{
    /**
     * 1. Mengolah aktivitas user menjadi laporan statistik & insight personal
     * Endpoint: GET /api/users/{userId}/insights
     */
    public function userInsights($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        // Olah data backlog & playtime dari tabel user_games
        $totalGames = UserGame::where('user_id', $userId)->count();
        $totalPlaytime = (int) UserGame::where('user_id', $userId)->sum('playtime');

        $statusBreakdown = UserGame::where('user_id', $userId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $completedCount = $statusBreakdown['Completed'] ?? 0;
        $completionRate = $totalGames > 0
            ? round(($completedCount / $totalGames) * 100, 1)
            : 0;

        // Olah data penilaian dari tabel reviews
        $reviewStats = Review::where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total_reviews,
                ROUND(AVG(rating), 1) as avg_overall,
                ROUND(AVG(gameplay_rating), 1) as avg_gameplay,
                ROUND(AVG(graphics_rating), 1) as avg_graphics,
                ROUND(AVG(story_rating), 1) as avg_story
            ')
            ->first();

        // Cari genre favorit berdasarkan jumlah game & playtime user
        $favoriteGenre = DB::table('user_games')
            ->join('games', 'user_games.game_id', '=', 'games.id')
            ->join('genres', 'games.genre_id', '=', 'genres.id')
            ->where('user_games.user_id', $userId)
            ->select(
                'genres.id',
                'genres.name',
                DB::raw('COUNT(user_games.id) as total_games'),
                DB::raw('SUM(user_games.playtime) as total_playtime')
            )
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('total_playtime')
            ->orderByDesc('total_games')
            ->first();

        // Ambil 5 riwayat aktivitas terakhir dari tabel activity_logs
        $recentActivities = ActivityLog::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get(['action', 'description', 'created_at']);

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'gaming_summary' => [
                'total_games_in_backlog' => $totalGames,
                'total_playtime_hours' => $totalPlaytime,
                'completion_rate_percent' => $completionRate,
                'status_distribution' => $statusBreakdown,
                'favorite_genre' => $favoriteGenre ? $favoriteGenre->name : 'Belum ada data',
            ],
            'review_behavior' => [
                'total_reviews_written' => (int) ($reviewStats->total_reviews ?? 0),
                'average_scores' => [
                    'overall' => (float) ($reviewStats->avg_overall ?? 0),
                    'gameplay' => (float) ($reviewStats->avg_gameplay ?? 0),
                    'graphics' => (float) ($reviewStats->avg_graphics ?? 0),
                    'story' => (float) ($reviewStats->avg_story ?? 0),
                ],
            ],
            'recent_activities' => $recentActivities,
        ], 200);
    }

    /**
     * 2. Rekomendasi game dari RAWG API berdasarkan genre favorit & riwayat main user
     * Endpoint: GET /api/users/{userId}/recommendations
     */
    public function recommendations($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        // Cari genre yang paling sering dimainkan user
        $topGenre = DB::table('user_games')
            ->join('games', 'user_games.game_id', '=', 'games.id')
            ->join('genres', 'games.genre_id', '=', 'genres.id')
            ->where('user_games.user_id', $userId)
            ->select('genres.id', 'genres.name', DB::raw('COUNT(*) as total'))
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('total')
            ->first();

        // Ambil daftar rawg_id yang sudah dimiliki user agar tidak direkomendasikan ulang
        $ownedRawgIds = DB::table('user_games')
            ->join('games', 'user_games.game_id', '=', 'games.id')
            ->where('user_games.user_id', $userId)
            ->whereNotNull('games.rawg_id')
            ->pluck('games.rawg_id')
            ->toArray();

        // Mapping nama genre lokal ke slug resmi milik RAWG API
        $genreMap = [
            'rpg' => 'role-playing-games-rpg',
            'fps' => 'shooter',
            'action' => 'action',
            'adventure' => 'adventure',
            'fighting' => 'fighting',
            'racing' => 'racing',
            'simulation' => 'simulation',
            'strategy' => 'strategy',
            'sports' => 'sports',
            'puzzle' => 'puzzle',
        ];

        $genreName = $topGenre ? $topGenre->name : 'Action';
        $genreSlug = $genreMap[strtolower($genreName)] ?? Str::slug($genreName);
        $rawgApiKey = env('RAWG_API_KEY');

        // Jika RAWG_API_KEY tersedia di .env, ambil rekomendasi langsung dari RAWG API
        if (!empty($rawgApiKey)) {
            $response = Http::get('https://api.rawg.io/api/games', [
                'key' => $rawgApiKey,
                'genres' => $genreSlug,
                'ordering' => '-rating',
                'page_size' => 15,
            ]);

            if ($response->successful()) {
                $recommended = collect($response->json('results'))
                    ->reject(fn ($game) => in_array($game['id'], $ownedRawgIds))
                    ->take(5)
                    ->values()
                    ->map(fn ($game) => [
                        'rawg_id' => $game['id'],
                        'title' => $game['name'],
                        'released' => $game['released'],
                        'rating' => $game['rating'],
                        'cover_image' => $game['background_image'],
                    ]);

                if ($recommended->isNotEmpty()) {
                    return response()->json([
                        'status' => 'success',
                        'source' => 'GameBacklog Insight Engine + RAWG API',
                        'based_on_favorite_genre' => $genreName,
                        'recommendations' => $recommended,
                    ], 200);
                }
            }
        }

        // Fallback: ambil dari database lokal jika RAWG kosong atau key belum diisi
        $ownedGameIds = UserGame::where('user_id', $userId)->pluck('game_id');
        $localFallback = Game::when($topGenre, fn ($q) => $q->where('genre_id', $topGenre->id))
            ->whereNotIn('id', $ownedGameIds)
            ->take(5)
            ->get(['id', 'rawg_id', 'title', 'developer', 'release_date', 'cover_image']);

        if ($localFallback->isEmpty()) {
            $localFallback = Game::whereNotIn('id', $ownedGameIds)
                ->take(5)
                ->get(['id', 'rawg_id', 'title', 'developer', 'release_date', 'cover_image']);
        }

        return response()->json([
            'status' => 'success',
            'source' => 'GameBacklog Insight Engine (Local Fallback)',
            'based_on_favorite_genre' => $genreName,
            'recommendations' => $localFallback,
        ], 200);
    }

    /**
     * 3. Statistik game & genre paling populer berdasarkan aktivitas seluruh user
     * Endpoint: GET /api/community/trending
     */
    public function communityTrending()
    {
        $mostPlayedGames = DB::table('user_games')
            ->join('games', 'user_games.game_id', '=', 'games.id')
            ->select(
                'games.id',
                'games.rawg_id',
                'games.title',
                'games.cover_image',
                DB::raw('COUNT(DISTINCT user_games.user_id) as total_players'),
                DB::raw('SUM(user_games.playtime) as total_community_playtime')
            )
            ->groupBy('games.id', 'games.rawg_id', 'games.title', 'games.cover_image')
            ->orderByDesc('total_players')
            ->orderByDesc('total_community_playtime')
            ->take(5)
            ->get();

        $topRatedGames = DB::table('reviews')
            ->join('games', 'reviews.game_id', '=', 'games.id')
            ->select(
                'games.id',
                'games.rawg_id',
                'games.title',
                DB::raw('ROUND(AVG(reviews.rating), 1) as average_rating'),
                DB::raw('COUNT(reviews.id) as total_reviews')
            )
            ->groupBy('games.id', 'games.rawg_id', 'games.title')
            ->orderByDesc('average_rating')
            ->orderByDesc('total_reviews')
            ->take(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'most_played_by_community' => $mostPlayedGames,
            'highest_rated_by_community' => $topRatedGames,
        ], 200);
    }
}