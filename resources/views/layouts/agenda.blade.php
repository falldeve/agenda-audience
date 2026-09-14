<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body style="margin:0;min-height:100vh;display:flex;flex-direction:column;background:#F4F2ED;color:#2A1A14;font-family:'DM Sans',system-ui,sans-serif">

        <header style="display:flex;align-items:center;justify-content:space-between;gap:24px;padding:14px 28px;background:#FFFFFF;border-bottom:1px solid rgba(42,26,20,.12)">
            <div style="display:flex;align-items:center;gap:14px;min-width:0">
                <div style="width:38px;height:38px;border-radius:8px;background:#2D5A27;color:#FFFFFF;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;letter-spacing:.04em">RS</div>
                <div style="display:flex;flex-direction:column;gap:2px;min-width:0">
                    <div style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#8A766C;font-weight:700">République du Sénégal</div>
                    <div style="font-size:15px;font-weight:700;color:#2A1A14">Agenda des audiences — Direction générale</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                <span style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#8A766C;font-weight:700">Démonstration</span>
                <div style="display:flex;gap:4px;padding:4px;background:#F0EDE6;border-radius:999px">
                    <button type="button" style="padding:8px 16px;border-radius:999px;border:none;background:#2D5A27;color:#FFFFFF;font-size:13px;font-weight:700;cursor:pointer">Espace secrétaire</button>
                    <button type="button" style="padding:8px 16px;border-radius:999px;border:none;background:transparent;color:#5A463D;font-size:13px;font-weight:700;cursor:pointer">Espace directeur</button>
                </div>
            </div>
        </header>

        <div style="display:flex;flex:1;min-height:0">

            <nav style="width:236px;flex:0 0 236px;background:#FFFFFF;border-right:1px solid rgba(42,26,20,.12);padding:22px 14px;display:flex;flex-direction:column;gap:6px">
                <div style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#8A766C;font-weight:700;padding:0 12px 10px">Secrétariat</div>

                @php
                    $lienActif = 'padding:10px 12px;border-radius:8px;background:#F6EAD6;color:#2D5A27;font-size:14px;font-weight:700;text-decoration:none';
                    $lienInactif = 'padding:10px 12px;border-radius:8px;background:transparent;color:#2A1A14;font-size:14px;font-weight:500;text-decoration:none';
                @endphp

                <a href="{{ route('agenda') }}" style="{{ request()->routeIs('agenda') ? $lienActif : $lienInactif }}">Agenda semaine</a>
                <a href="{{ route('nouvelle-demande') }}" style="{{ request()->routeIs('nouvelle-demande') ? $lienActif : $lienInactif }}">Nouvelle demande</a>
                <a href="#" style="{{ $lienInactif }}">Fiche audience</a>

                <div style="margin-top:auto;padding:14px 12px 0;border-top:1px solid rgba(42,26,20,.10);font-size:12px;color:#8A766C;line-height:1.6">Connectée en tant que<br><span style="color:#2A1A14;font-weight:700">{{ auth()->user()->name }}</span><br>Secrétariat particulier</div>

                <form method="POST" action="{{ route('logout') }}" style="padding:10px 12px 0">
                    @csrf
                    <button type="submit" data-test="logout-button"
                            style="width:100%;padding:9px 14px;border-radius:999px;border:1px solid rgba(42,26,20,.20);background:#FFFFFF;color:#5A463D;font-size:13px;font-weight:700;font-family:inherit;cursor:pointer">
                        Se déconnecter
                    </button>
                </form>
            </nav>

            <main style="flex:1;min-width:0;padding:28px 32px 48px">
                {{ $slot }}
            </main>

        </div>

    </body>
</html>
