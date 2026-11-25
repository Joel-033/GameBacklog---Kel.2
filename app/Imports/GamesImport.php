<?php

namespace App\Imports;

use App\Models\Game;
use App\Models\Genre;
use App\Models\Platform;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class GamesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $title = trim((string) ($row['judul_game'] ?? $row['title'] ?? ''));

            if ($title === '' || Game::where('title', $title)->exists()) {
                continue;
            }

            $genreName = trim((string) ($row['genre'] ?? ''));
            if ($genreName === '' || $genreName === '-') {
                $genreName = 'General';
            }
            $genre = Genre::firstOrCreate(['name' => $genreName]);

            $rawDate = $row['tanggal_rilis'] ?? $row['release_date'] ?? null;
            $releaseDate = now()->toDateString();

            if (!empty($rawDate) && $rawDate !== '-') {
                try {
                    if (is_numeric($rawDate)) {
                        $releaseDate = Date::excelToDateTimeObject($rawDate)->format('Y-m-d');
                    } else {
                        $releaseDate = Carbon::parse($rawDate)->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $releaseDate = now()->toDateString();
                }
            }

            $developer = trim((string) ($row['developer'] ?? '-'));
            $publisher = trim((string) ($row['publisher'] ?? '-'));
            $description = trim((string) ($row['deskripsi'] ?? $row['description'] ?? ''));

            // 1. Cek jika admin mengisi kolom cover_image secara manual di Excel
            $coverImage = trim((string) ($row['cover_image'] ?? $row['cover'] ?? ''));
            if ($coverImage === '' || $coverImage === '-') {
                $coverImage = null;
            }

            // 2. Jika kosong, otomatis cari gambar cover dari RAWG API berdasarkan judul game
            if (!$coverImage) {
                $coverImage = $this->fetchCoverFromRawg($title);
            }

            $game = Game::create([
                'title'        => $title,
                'genre_id'     => $genre->id,
                'developer'    => $developer !== '' ? $developer : '-',
                'publisher'    => $publisher !== '' ? $publisher : '-',
                'release_date' => $releaseDate,
                'description'  => ($description !== '' && $description !== '-') ? $description : null,
                'cover_image'  => $coverImage,
            ]);

            $rawPlatforms = trim((string) ($row['platform'] ?? $row['platforms'] ?? ''));
            if ($rawPlatforms !== '' && $rawPlatforms !== '-') {
                $platformNames = array_filter(array_map('trim', explode(',', $rawPlatforms)));
                $platformIds = [];

                foreach ($platformNames as $pName) {
                    $platform = Platform::firstOrCreate(['name' => $pName]);
                    $platformIds[] = $platform->id;
                }

                if (!empty($platformIds)) {
                    $game->platforms()->sync($platformIds);
                }
            }
        }
    }

    private function fetchCoverFromRawg(string $title): ?string
    {
        $apiKey = env('RAWG_API_KEY') ?: config('services.rawg.key');

        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::timeout(5)->get('https://api.rawg.io/api/games', [
                'key'       => $apiKey,
                'search'    => $title,
                'page_size' => 1,
            ]);

            if ($response->successful()) {
                return $response->json('results.0.background_image');
            }
        } catch (\Exception $e) {
            // Tetap lanjut import jika koneksi lambat/gagal
        }

        return null;
    }
}