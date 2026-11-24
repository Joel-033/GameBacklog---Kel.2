
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $game['name'] }} - GameLog</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #151515;
            color: white;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .back {
            display: inline-block;
            color: #aaa;
            text-decoration: none;
            margin-bottom: 25px;
        }

        .back:hover {
            color: white;
        }

        .hero {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .metadata {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 20px 0;
        }

        .tag {
            background: #303030;
            padding: 9px 14px;
            border-radius: 20px;
            color: #ddd;
        }

        .section {
            background: #252525;
            padding: 22px;
            border-radius: 12px;
            margin-top: 22px;
        }

        .section h2 {
            margin-top: 0;
        }

        .info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
        }

        .info-item p {
            color: #aaa;
            margin-bottom: 5px;
        }

        .info-item strong {
            line-height: 1.5;
        }

        .description {
            line-height: 1.8;
            color: #ddd;
            white-space: pre-line;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0;
        }

        .success {
            background: #166534;
        }

        .error {
            background: #991b1b;
        }

        .add-button {
            background: #367bf5;
            color: white;
            border: none;
            padding: 13px 22px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
        }

        .add-button:hover {
            background: #2563eb;
        }

        footer {
            margin-top: 35px;
            color: #aaa;
            font-size: 13px;
        }

        footer a {
            color: #aaa;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            .hero {
                height: 230px;
            }

            h1 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Tombol kembali ke katalog -->
    <a href="{{ url('/rawg-catalog') }}?search={{ urlencode(request('search', '')) }}"
       class="back">

        &larr; Back to Catalog

    </a>


    <!-- Cover Game -->
    @if (!empty($game['background_image']))

        <img
            src="{{ $game['background_image'] }}"
            alt="{{ $game['name'] }}"
            class="hero"
        >

    @endif


    <!-- Nama Game -->
    <h1>{{ $game['name'] }}</h1>


    <!-- Notifikasi berhasil -->
    @if (session('success'))

        <div class="alert success">
            {{ session('success') }}
        </div>

    @endif


    <!-- Notifikasi error -->
    @if (session('error'))

        <div class="alert error">
            {{ session('error') }}
        </div>

    @endif


    <!-- Tombol Add to My Games -->
    @auth

        <form
            action="{{ route('rawg.add', [
                'id' => $game['id'],
                'search' => request('search')
            ]) }}"
            method="POST"
            style="margin: 20px 0;"
        >

            @csrf

            <button type="submit" class="add-button">
                + Add to My Games
            </button>

        </form>

    @else

        <p>
            <a href="{{ route('login') }}" style="color: #60a5fa;">
                Login to add this game to your library.
            </a>
        </p>

    @endauth


    <!-- Genre -->
    <div class="metadata">

        @foreach ($game['genres'] ?? [] as $genre)

            <span class="tag">
                {{ $genre['name'] }}
            </span>

        @endforeach

    </div>


    <!-- Informasi Utama -->
    <div class="section">

        <h2>Game Information</h2>

        <div class="info">

            <div class="info-item">
                <p>Release Date</p>

                <strong>
                    {{ $game['released'] ?? 'Unknown' }}
                </strong>
            </div>


            <div class="info-item">
                <p>Rating</p>

                <strong>
                    {{ $game['rating'] ?? 'N/A' }}
                </strong>
            </div>


            <div class="info-item">
                <p>Metacritic</p>

                <strong>
                    {{ $game['metacritic'] ?? 'N/A' }}
                </strong>
            </div>


            <div class="info-item">
                <p>ESRB Rating</p>

                <strong>
                    {{ $game['esrb_rating']['name'] ?? 'Unknown' }}
                </strong>
            </div>


            <div class="info-item">
                <p>Developer</p>

                <strong>
                    {{ collect($game['developers'] ?? [])
                        ->pluck('name')
                        ->implode(', ') ?: 'Unknown' }}
                </strong>
            </div>


            <div class="info-item">
                <p>Publisher</p>

                <strong>
                    {{ collect($game['publishers'] ?? [])
                        ->pluck('name')
                        ->implode(', ') ?: 'Unknown' }}
                </strong>
            </div>

        </div>

    </div>


    <!-- Platform -->
    <div class="section">

        <h2>Available Platforms</h2>

        <div class="metadata">

            @forelse ($game['platforms'] ?? [] as $item)

                <span class="tag">
                    {{ $item['platform']['name'] ?? 'Unknown' }}
                </span>

            @empty

                <p>Platform information unavailable.</p>

            @endforelse

        </div>

    </div>


    <!-- Deskripsi Game -->
    <div class="section">

        <h2>About This Game</h2>

        <div class="description">
            {{ strip_tags($game['description'] ?? 'No description available.') }}
        </div>

    </div>


    <!-- Footer -->
    <footer>

        Game information and images provided by

        <a href="https://rawg.io/"
           target="_blank"
           rel="noopener">

            RAWG

        </a>.

    </footer>

</div>

</body>
</html>
