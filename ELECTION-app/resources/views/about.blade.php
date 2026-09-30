<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f9f9ff">
    <title>About this election system · District 23 FYS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/splash.css?v={{ filemtime(public_path('css/splash.css')) }}">
</head>
<body class="splash about-page">
    <main class="splash-stage" aria-labelledby="about-title">
        <div class="ambient-glow" aria-hidden="true"></div>
        <article class="about-card">
            <img class="about-logo" width="96" height="96" src="/images/uecfi-logo.png" alt="District 23 FYS Official Insignia">
            <p class="district-label">District 23 FYS</p>
            <h1 id="about-title">About this election system</h1>
            <p class="about-copy">A simple gift project by the FYS District 23 Secretary, Hmna. Bianca Vanessa F. Salada, for the FYS of District 23. Since they cannot conduct the election face-to-face, this project provides a way for members to elect their future officers through a digital election system that aims to make the experience as similar as possible to an in-person election.</p>
            <div class="about-actions">
                <a class="about-back" href="{{ route('splash') }}">Back to start</a>
                <a class="about-continue" href="{{ route('home') }}">Continue to election <span aria-hidden="true">→</span></a>
            </div>
        </article>
    </main>
</body>
</html>
