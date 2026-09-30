<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f9f9ff">
    <title>District 23 FYS · Election of Officers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/splash.css?v={{ filemtime(public_path('css/splash.css')) }}">
    <script src="/js/splash.js?v={{ filemtime(public_path('js/splash.js')) }}" defer></script>
</head>
<body class="splash" data-home-url="/home">
    <main class="splash-stage" aria-labelledby="election-title">
        <div class="ambient-glow" aria-hidden="true"></div>
        <section class="splash-center">
            <div class="insignia">
                <div class="insignia-halo" aria-hidden="true"></div>
                <div class="insignia-orbit" aria-hidden="true"></div>
                <div class="insignia-frame">
                    <div class="insignia-inner">
                        <img class="insignia-image" width="140" height="140" fetchpriority="high"
                            src="/images/uecfi-logo.png"
                            alt="District 23 FYS Official Insignia">
                    </div>
                </div>
            </div>
            <div class="splash-titles">
                <p class="district-label">District 23 FYS</p>
                <h1 id="election-title">Election of Officers</h1>
                <p class="election-term">2027–2030</p>
            </div>
        </section>
        <footer class="splash-footer">
            <div class="splash-progress" aria-hidden="true"><span></span></div>
            <div class="splash-links">
                <a class="splash-about-link" href="{{ route('about') }}">About this election system</a>
                <a class="splash-skip" href="/home">Continue <span aria-hidden="true">→</span></a>
            </div>
            <noscript><p>Select Continue to enter.</p></noscript>
        </footer>
    </main>
</body>
</html>


