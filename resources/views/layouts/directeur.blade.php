<!DOCTYPE html>
<html lang="fr">
@include('partials.head')
<body style="background:#F4F2ED;margin:0;min-height:100vh;font-family:'DM Sans',system-ui,sans-serif;color:#2A1A14">

@php
    $enAttente = \App\Models\Audience::where('statut', 'programmee')->count();
@endphp

<header style="display:flex;align-items:center;justify-content:space-between;gap:24px;padding:14px 28px;background:#FFFFFF;border-bottom:1px solid rgba(42,26,20,.12)">
    <div style="display:flex;align-items:center;gap:14px">
        <div style="width:38px;height:38px;border-radius:8px;background:#2D5A27;color:#FFF;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px">RS</div>
        <div>
            <div style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#8A766C;font-weight:700">République du Sénégal</div>
            <div style="font-size:15px;font-weight:700;color:#2A1A14">Agenda des audiences — Direction générale</div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:16px;font-size:13px">
        <span style="color:#5A463D">{{ auth()->user()->name }}</span>
        <button onclick="document.getElementById('form-logout').submit()" style="background:none;border:none;color:#2D5A27;font-weight:700;cursor:pointer;font-size:13px">Déconnexion</button>
        <form id="form-logout" method="POST" action="{{ route('logout') }}" style="display:none">@csrf</form>
    </div>
</header>

<nav style="display:flex;gap:4px;padding:0 28px;background:#FFFFFF;border-bottom:1px solid rgba(42,26,20,.12)">
    @php $actif1 = request()->routeIs('directeur.audiences'); @endphp
    <a href="{{ route('directeur.audiences') }}"
       style="padding:14px 16px;text-decoration:none;font-size:14px;font-weight:700;color:{{ $actif1 ? '#2D5A27' : '#5A463D' }};border-bottom:2px solid {{ $actif1 ? '#2D5A27' : 'transparent' }}">
        Mes audiences
    </a>
    @php $actif2 = request()->routeIs('directeur.decisions'); @endphp
    <a href="{{ route('directeur.decisions') }}"
       style="padding:14px 16px;text-decoration:none;font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;color:{{ $actif2 ? '#2D5A27' : '#5A463D' }};border-bottom:2px solid {{ $actif2 ? '#2D5A27' : 'transparent' }}">
        À valider
        @if ($enAttente > 0)
            <span style="background:#2D5A27;color:#FFF;font-size:11px;font-weight:700;min-width:20px;height:20px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;padding:0 6px">{{ $enAttente }}</span>
        @endif
    </a>
</nav>

<main style="max-width:900px;margin:0 auto;padding:28px 24px 48px">
    {{ $slot }}
</main>

@fluxScripts
</body>
</html>