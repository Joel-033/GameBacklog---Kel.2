
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - GameBacklog</title>

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


        <!-- Register Card -->
        <section class="auth-card">

            <!-- Header -->
            <div class="auth-header">
                <h1>GAMEBACKLOG</h1>
                <p>WII COLLECTION SYSTEM</p>
            </div>


            <!-- Introduction -->
            <div class="auth-intro">

                <div class="auth-icon">
                    🎮
                </div>

                <h2>Create Your Account</h2>

                <p>
                    Join GameBacklog and start your gaming collection.
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


            <!-- Register Form -->
            <form action="/register"
                  method="POST"
                  class="auth-form">

                @csrf


                <!-- Name -->
                <div class="auth-field">

                    <label for="name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Enter your name"
                        autocomplete="name"
                        required
                    >

                </div>


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
                        placeholder="Create a password"
                        autocomplete="new-password"
                        required
                    >

                </div>


                <!-- Confirm Password -->
                <div class="auth-field">

                    <label for="password_confirmation">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Confirm your password"
                        autocomplete="new-password"
                        required
                    >

                </div>


                <!-- Register Button -->
                <button type="submit" class="auth-submit">

                    Create Account

                    <span>→</span>

                </button>

            </form>


            <!-- Login Link -->
            <div class="auth-register">

                <p>
                    Sudah punya akun?
                    <a href="/login">Login</a>
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
