<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

{{-- Thème institutionnel : déclaré après @vite pour passer devant les styles de base de Tailwind --}}
<style>
    :root {
        --cream: #FDF6EC;
        --cream-deep: #F6EAD6;
        --leaf-green: #2D5A27;
        --leaf-green-bright: #3E7A36;
        --ink-900: #2A1A14;
        --ink-500: #8A766C;
        --border-soft: rgba(42, 26, 20, 0.12);
        --font-display: 'Playfair Display', Georgia, serif;
        --font-body: 'DM Sans', system-ui, sans-serif;
    }

    body {
        background: var(--cream);
        color: var(--ink-900);
        font-family: var(--font-body);
    }

    h1, h2, h3 {
        font-family: var(--font-display);
        color: var(--leaf-green);
    }
</style>
