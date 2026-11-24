
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - GameBacklog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="wii-body auth-page" data-theme="classic">

    <main class="auth-container">

        <!-- Theme Switcher -->
        <div class="auth-theme-control">
            <button type="button"
                    class="theme-toggle"
                    id="wii-theme-toggle"
                    title="Aktifkan Wii Midnight">
                🌙
            </button>
        </div>


        <!-- Login Card -->
        <section class="auth-card">

            <!-- Header -->
            <div class="auth-header">
                <h1>GAMEBACKLOG</h1>
                <p>WII COLLECTION SYSTEM</p>
            </div>


            <!-- Login Introduction -->
            <div class="auth-intro">

                <div class="auth-icon">
                    🎮
                </div>

                <h2>Welcome Back!</h2>

                <p>
                    Sign in to continue your gaming journey.
                </p>

            </div>


            <!-- Error Messages -->
            @if ($errors->any())

                <div class="auth-errors">

                    @foreach ($errors->all() as $error)

                        <p>⚠ {{ $error }}</p>

                    @endforeach

                </div>

            @endif


            <!-- Login Form -->
            <form action="/login"
                  method="POST"
                  class="auth-form">

                @csrf


                <!-- Email -->
                <div class="auth-field">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter your email"
                        autocomplete="email"
                        required
                    >

                </div>


                <!-- Password -->
                <div class="auth-field">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >

                </div>


                <!-- Login Button -->
                <button type="submit" class="auth-submit">

                    Login

                    <span>→</span>

                </button>

            </form>


            <!-- Register Link -->
            <div class="auth-register">

                <p>
                    Belum punya akun?
                    <a href="/register">Register</a>
                </p>

            </div>


            <!-- Footer -->
            <div class="auth-footer">

                GAMEBACKLOG · WII COLLECTION SYSTEM

            </div>

        </section>


        <!-- Decorative Text -->
        <div class="auth-bottom-text">
            YOUR GAME COLLECTION STARTS HERE
        </div>

    </main>


    <!-- Theme Switcher Script -->
    <script>

        document.addEventListener('DOMContentLoaded', function () {

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

        });

    </script>

</body>
</html>
