
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - GameBacklog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="wii-body dashboard-page" data-theme="classic">

    <main class="dashboard-container">

        <!-- Top Bar -->
        <header class="dashboard-top">

            <span class="wii-brand">GAMEBACKLOG</span>

            <div class="dashboard-top-actions">

                <span class="dashboard-role">
                    {{ Auth::user()->role }}
                </span>

                <button type="button"
                        class="theme-toggle"
                        id="wii-theme-toggle"
                        title="Aktifkan Wii Midnight">
                    🌙
                </button>

            </div>

        </header>


        <!-- Welcome Card -->
        <section class="dashboard-card">

            <div class="dashboard-welcome">

                <div class="dashboard-avatar">
                    🎮
                </div>

                <p class="dashboard-subtitle">
                    GAMEBACKLOG · WII COLLECTION SYSTEM
                </p>

                <h1>Welcome Back!</h1>

                <h2>
                    Hello, {{ Auth::user()->name }}!
                </h2>

                <p class="dashboard-description">
                    Your gaming journey continues here.
                    Explore your collection and enjoy your games.
                </p>

            </div>


            <!-- Main Navigation -->
            <div class="dashboard-actions">

                <a href="{{ route('games.index') }}"
                   class="dashboard-menu-card">

                    <div class="dashboard-menu-icon">
                        🎮
                    </div>

                    <div>
                        <h3>My Collection</h3>
                        <p>Explore your game collection</p>
                    </div>

                    <span class="dashboard-arrow">›</span>

                </a>


                <div class="dashboard-menu-card account-card">

                    <div class="dashboard-menu-icon">
                        👤
                    </div>

                    <div>
                        <h3>My Account</h3>
                        <p>View your account information</p>
                    </div>

                    <span class="dashboard-arrow">›</span>

                </div>

            </div>


            <!-- Account Information -->
            <div class="dashboard-account">

                <div class="dashboard-section-heading">
                    <h3>Account Information</h3>
                    <span>PROFILE</span>
                </div>

                <div class="dashboard-info-grid">

                    <div class="dashboard-info-item">
                        <span>Full Name</span>
                        <strong>{{ Auth::user()->name }}</strong>
                    </div>

                    <div class="dashboard-info-item">
                        <span>Email Address</span>
                        <strong>{{ Auth::user()->email }}</strong>
                    </div>

                    <div class="dashboard-info-item">
                        <span>Account Role</span>
                        <strong>{{ Auth::user()->role }}</strong>
                    </div>

                </div>

            </div>


            <!-- Bottom Actions -->
            <div class="dashboard-bottom-actions">

                <a href="{{ route('games.index') }}"
                   class="dashboard-enter-button">

                    Open Menu

                    <span>→</span>

                </a>


                <form action="/logout" method="POST">
                    @csrf

                    <button type="submit"
                            class="dashboard-logout-button"
                            onclick="return confirm('Apakah kamu yakin ingin logout?')">

                        <span>↪</span>
                        Logout

                    </button>

                </form>

            </div>


            <!-- Footer -->
            <footer class="dashboard-footer">
                GAMEBACKLOG · WII COLLECTION SYSTEM
            </footer>

        </section>


        <div class="dashboard-bottom-text">
            YOUR GAME COLLECTION STARTS HERE
        </div>

    </main>


    <!-- Theme Switcher -->
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
