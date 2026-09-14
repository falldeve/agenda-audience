<div>
    <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;margin:0 0 6px;color:#2A1A14">Mes audiences</h1>
    <div style="font-size:14px;color:#5A463D;margin-bottom:16px;text-transform:capitalize">
        {{ $libelle }} · {{ $audiences->count() }} {{ $audiences->count() > 1 ? 'audiences' : 'audience' }}
    </div>

    <div style="display:flex;gap:8px;margin-bottom:24px">
        <button wire:click="voirJour"
                style="padding:8px 16px;border-radius:999px;font-size:13px;font-weight:700;cursor:pointer;
                       {{ $vue === 'jour' ? 'background:#2D5A27;color:#FFF;border:none' : 'background:#FFF;color:#2A1A14;border:1px solid rgba(42,26,20,.20)' }}">
            Aujourd'hui
        </button>
        <button wire:click="voirSemaine"
                style="padding:8px 16px;border-radius:999px;font-size:13px;font-weight:700;cursor:pointer;
                       {{ $vue === 'semaine' ? 'background:#2D5A27;color:#FFF;border:none' : 'background:#FFF;color:#2A1A14;border:1px solid rgba(42,26,20,.20)' }}">
            Cette semaine
        </button>
    </div>

    @forelse ($audiences as $a)
        <div style="display:flex;align-items:center;gap:20px;background:#FFF;border:1px solid rgba(42,26,20,.12);border-left:3px solid {{ $a->statut === 'validee' ? '#2D5A27' : '#1F4E79' }};border-radius:10px;padding:18px 22px;margin-bottom:14px;flex-wrap:wrap">
            <div style="min-width:92px">
                @if ($vue === 'semaine')
                    <div style="font-size:12px;color:#8A766C;text-transform:capitalize">{{ $a->creneau->translatedFormat('l j') }}</div>
                @endif
                <div style="font-family:'Playfair Display',serif;font-size:26px;color:#2A1A14">{{ $a->creneau->format('H\hi') }}</div>
            </div>

            <div style="flex:1;min-width:200px">
                <div style="font-size:16px;font-weight:700;color:#2A1A14">{{ $a->demandeur_nom }}</div>
                <div style="font-size:13px;color:#5A463D">{{ $a->demandeur_organisation }}</div>
                <div style="font-size:13px;color:#5A463D;margin-top:2px">{{ $a->objet }}</div>
                @if ($a->lettre_scannee)
                    <a href="{{ asset('storage/'.$a->lettre_scannee) }}" target="_blank" style="font-size:12px;color:#2D5A27;font-weight:700;text-decoration:underline">Lettre scannée</a>
                @endif
            </div>

            <div>
                @if ($a->statut === 'validee')
                    <span style="font-size:12px;font-weight:700;padding:5px 12px;border-radius:999px;background:#EAF0E6;color:#2D5A27">Validée</span>
                @else
                    <span style="font-size:12px;font-weight:700;padding:5px 12px;border-radius:999px;background:#E7EEF5;color:#1F4E79">En attente</span>
                @endif
            </div>
        </div>
    @empty
        <div style="background:#FFF;border:1px solid rgba(42,26,20,.12);border-radius:10px;padding:40px;text-align:center;color:#8A766C">
            Aucune audience prévue {{ $vue === 'jour' ? "aujourd'hui" : 'cette semaine' }}.
        </div>
    @endforelse
</div>