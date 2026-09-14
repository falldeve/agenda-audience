<div>
    <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;margin:0 0 6px;color:#2A1A14">Audiences à valider</h1>
    <div style="font-size:14px;color:#5A463D;margin-bottom:24px">
        {{ $audiences->count() }} {{ $audiences->count() > 1 ? 'audiences attendent' : 'audience attend' }} votre décision.
    </div>

    @if (session('message'))
        <div style="margin-bottom:16px;padding:12px 16px;background:#EAF0E6;color:#2D5A27;border-radius:8px;font-size:14px">{{ session('message') }}</div>
    @endif

    @forelse ($audiences as $a)
        <div style="display:flex;align-items:center;gap:20px;background:#FFF;border:1px solid rgba(42,26,20,.12);border-left:3px solid #1F4E79;border-radius:10px;padding:18px 22px;margin-bottom:14px;flex-wrap:wrap">
            <div style="min-width:92px">
                <div style="font-family:'Playfair Display',serif;font-size:26px;color:#2A1A14">{{ $a->creneau->format('H\hi') }}</div>
                <div style="font-size:12px;color:#8A766C">{{ $a->creneau->translatedFormat('D d M') }}</div>
            </div>

            <div style="flex:1;min-width:200px">
                <div style="font-size:16px;font-weight:700;color:#2A1A14">{{ $a->demandeur_nom }}</div>
                <div style="font-size:13px;color:#5A463D">{{ $a->demandeur_organisation }}</div>
                <div style="font-size:13px;color:#5A463D;margin-top:2px">{{ $a->objet }}</div>
                @if ($a->lettre_scannee)
                    <a href="{{ asset('storage/'.$a->lettre_scannee) }}" target="_blank" style="font-size:12px;color:#2D5A27;font-weight:700;text-decoration:underline">Lettre scannée</a>
                @endif
            </div>

            <div style="display:flex;gap:8px">
                <button wire:click="ouvrirRefus({{ $a->id }})" style="padding:9px 16px;border-radius:999px;border:1px solid rgba(42,26,20,.20);background:#FFF;color:#2A1A14;font-size:13px;font-weight:700;cursor:pointer">Refuser</button>
                <button wire:click="valider({{ $a->id }})" style="padding:9px 18px;border-radius:999px;border:none;background:#2D5A27;color:#FFF;font-size:13px;font-weight:700;cursor:pointer">Valider</button>
            </div>
        </div>
    @empty
        <div style="background:#FFF;border:1px solid rgba(42,26,20,.12);border-radius:10px;padding:40px;text-align:center;color:#8A766C">
            Aucune audience en attente de décision.
        </div>
    @endforelse

    @if ($audienceARefuser)
        @php $cible = $audiences->firstWhere('id', $audienceARefuser) ?? \App\Models\Audience::find($audienceARefuser); @endphp
        <div style="position:fixed;inset:0;background:rgba(42,26,20,.45);display:flex;align-items:center;justify-content:center;z-index:50">
            <div style="background:#FFF;border-radius:12px;padding:24px 26px;max-width:440px;width:90%">
                <h2 style="font-family:'Playfair Display',serif;font-size:20px;margin:0 0 6px;color:#2A1A14">Refuser l'audience</h2>
                <div style="font-size:13px;color:#5A463D;margin-bottom:16px">{{ $cible?->demandeur_nom }} · {{ $cible?->objet }}</div>

                <label style="display:block;font-size:13px;font-weight:700;color:#2A1A14;margin-bottom:6px">Motif (facultatif)</label>
                <textarea wire:model="motifRefus" rows="3" style="width:100%;padding:10px 12px;border:1px solid rgba(42,26,20,.22);border-radius:7px;font-family:inherit;font-size:13px;box-sizing:border-box"></textarea>

                <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:18px">
                    <button wire:click="fermerRefus" style="padding:9px 16px;border-radius:999px;border:1px solid rgba(42,26,20,.20);background:#FFF;color:#2A1A14;font-size:13px;font-weight:700;cursor:pointer">Annuler</button>
                    <button wire:click="confirmerRefus" style="padding:9px 18px;border-radius:999px;border:none;background:#A33A2A;color:#FFF;font-size:13px;font-weight:700;cursor:pointer">Confirmer le refus</button>
                </div>
            </div>
        </div>
    @endif
</div>