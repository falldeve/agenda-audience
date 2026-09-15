<div>

    @if (session('message'))
        <div style="margin-bottom:16px;padding:12px 16px;border-radius:8px;border-left:3px solid #2D5A27;background:#EAF0E6;color:#2A1A14;font-size:13px;font-weight:700">{{ session('message') }}</div>
    @endif

    {{-- En-tête de page --}}
    <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:24px;flex-wrap:wrap;margin-bottom:20px">
        <div>
            <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;margin:0 0 6px;color:#2A1A14">Agenda de la semaine</h1>
            <div style="font-size:14px;color:#5A463D">
                {{ Str::ucfirst($jours->first()->translatedFormat('l d')) }} — {{ $jours->last()->translatedFormat('l d F Y') }}
                · {{ $nbAudiencesSemaine }} audience{{ $nbAudiencesSemaine > 1 ? 's' : '' }} programmée{{ $nbAudiencesSemaine > 1 ? 's' : '' }}
            </div>

            <div style="display:flex;gap:6px;margin-top:12px;flex-wrap:wrap">
                <button type="button" wire:click="semainePrecedente"
                        style="padding:8px 14px;border-radius:999px;border:1px solid rgba(42,26,20,.12);background:#FFFFFF;color:#5A463D;font-size:13px;font-weight:700;cursor:pointer">
                    ‹ Précédente
                </button>
                <button type="button" wire:click="semaineActuelle"
                        style="padding:8px 14px;border-radius:999px;border:none;background:#2D5A27;color:#FFFFFF;font-size:13px;font-weight:700;cursor:pointer">
                    Aujourd'hui
                </button>
                <button type="button" wire:click="semaineSuivante"
                        style="padding:8px 14px;border-radius:999px;border:1px solid rgba(42,26,20,.12);background:#FFFFFF;color:#5A463D;font-size:13px;font-weight:700;cursor:pointer">
                    Suivante ›
                </button>
            </div>
        </div>

        <div style="display:flex;gap:18px;flex-wrap:wrap;align-items:center">
            <span style="display:flex;align-items:center;gap:7px;font-size:13px;color:#5A463D"><span style="width:11px;height:11px;border-radius:3px;background:#2D5A27;display:inline-block"></span>Validée</span>
            <span style="display:flex;align-items:center;gap:7px;font-size:13px;color:#5A463D"><span style="width:11px;height:11px;border-radius:3px;background:#1F4E79;display:inline-block"></span>En attente du directeur</span>
            <span style="display:flex;align-items:center;gap:7px;font-size:13px;color:#5A463D"><span style="width:11px;height:11px;border-radius:3px;background:#8A766C;display:inline-block"></span>Échue</span>
            <span style="display:flex;align-items:center;gap:7px;font-size:13px;color:#5A463D"><span style="width:11px;height:11px;border-radius:3px;background:#2D5A27;display:inline-block"></span>Tenue</span>
            <span style="display:flex;align-items:center;gap:7px;font-size:13px;color:#5A463D"><span style="width:11px;height:11px;border-radius:3px;background:#B4620A;display:inline-block"></span>Non honorée</span>
        </div>
    </div>

    {{-- Grille de la semaine --}}
    <div style="overflow-x:auto">
        <div style="min-width:720px;background:#FFFFFF;border:1px solid rgba(42,26,20,.12);border-radius:10px;overflow:hidden">

            <div style="display:grid;grid-template-columns:78px repeat(5,minmax(0,1fr));background:#F7F5F0;border-bottom:1px solid rgba(42,26,20,.12)">
                <div style="padding:12px 10px"></div>
                @foreach ($jours as $jour)
                    <div style="padding:12px 10px;font-size:13px;font-weight:700;color:#2A1A14;border-left:1px solid rgba(42,26,20,.08)">
                        {{ Str::ucfirst($jour->translatedFormat('l d')) }}
                    </div>
                @endforeach
            </div>

            @foreach ($heures as $heure)
                <div style="display:grid;grid-template-columns:78px repeat(5,minmax(0,1fr));border-bottom:1px solid rgba(42,26,20,.08);min-height:64px">
                    <div style="padding:10px;font-size:12px;color:#8A766C;font-weight:700">
                        {{ sprintf('%02d:00', $heure) }}
                    </div>

                    @foreach ($jours as $jour)
                        @php($cle = $jour->format('Y-m-d').'-'.$heure)

                        <div style="padding:6px;border-left:1px solid rgba(42,26,20,.08);display:flex;flex-direction:column;gap:5px">
                            @foreach ($parCase[$cle] ?? collect() as $audience)
                                @php([$libelleStatut, $traitStatut, $fondStatut] = match ($audience->statut) {
                                    'validee' => ['Validée', '#2D5A27', '#EAF0E6'],
                                    'echue' => ['Échue', '#8A766C', '#EDE9E3'],
                                    'tenue' => ['Tenue', '#2D5A27', '#EAF0E6'],
                                    'non_honoree' => ['Non honorée', '#B4620A', '#F6E9DC'],
                                    'reportee' => ['Reportée', '#B4620A', '#F6E9DC'],
                                    default => ['En attente', '#1F4E79', '#E7EEF5'],
                                })

                                <a href="{{ route('audience.fiche', $audience) }}"
                                   style="display:block;text-align:left;border:none;border-radius:7px;padding:7px 9px;cursor:pointer;text-decoration:none;border-left:3px solid {{ $traitStatut }};background:{{ $fondStatut }};color:#2A1A14">
                                    <span style="font-weight:700;font-size:12px;display:block">{{ $audience->creneau->format('H:i') }} · {{ $audience->demandeur_nom }}</span>
                                    <span style="font-size:11.5px;display:block;line-height:1.35;opacity:.92">{{ $audience->objet }}</span>

                                    @if (in_array($audience->statut, ['echue', 'tenue', 'non_honoree'], true))
                                        <span style="display:inline-block;margin-top:4px;padding:1px 7px;border-radius:999px;background:#FFFFFF;color:{{ $traitStatut }};font-size:10px;font-weight:700;letter-spacing:.04em">{{ $libelleStatut }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach

        </div>
    </div>

    {{-- Demandes à placer --}}
    <section style="margin-top:28px;background:#FFFFFF;border:1px solid rgba(42,26,20,.12);border-radius:10px;padding:20px 22px">
        <div style="display:flex;align-items:baseline;justify-content:space-between;gap:18px;margin-bottom:16px">
            <h2 style="font-family:'Playfair Display',serif;font-size:19px;font-weight:700;margin:0;color:#2A1A14">À placer</h2>
            <span style="font-size:12px;color:#8A766C;font-weight:700">{{ $aPlacer->count() }} demande{{ $aPlacer->count() > 1 ? 's' : '' }} en attente</span>
        </div>

        @forelse ($aPlacer as $audience)
            <div style="display:flex;align-items:center;justify-content:space-between;gap:18px;padding:13px 16px;background:#F7F5F0;border:1px solid rgba(42,26,20,.08);border-radius:8px;flex-wrap:wrap;margin-bottom:10px">
                <div style="min-width:0">
                    <div style="font-size:14px;font-weight:700;color:#2A1A14">{{ $audience->demandeur_nom }} — <span style="font-weight:400;color:#5A463D">{{ $audience->demandeur_organisation }}</span></div>
                    <div style="font-size:13px;color:#5A463D;margin-top:2px">{{ $audience->objet }}</div>
                    <div style="font-size:12px;color:#8A766C;margin-top:4px">Courrier reçu le {{ $audience->recue_le->format('d/m/Y') }}</div>
                </div>

                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                    <a href="{{ route('audience.fiche', $audience) }}"
                       style="padding:9px 16px;border-radius:999px;border:1px solid rgba(42,26,20,.20);background:#FFFFFF;color:#2A1A14;font-size:13px;font-weight:700;text-decoration:none">
                        Voir la fiche
                    </a>

                    <button type="button" wire:click="ouvrirProgrammation({{ $audience->id }})"
                            style="padding:9px 18px;border-radius:999px;border:none;background:#2D5A27;color:#FFFFFF;font-size:13px;font-weight:700;cursor:pointer">
                        Programmer
                    </button>
                </div>
            </div>
        @empty
            <p style="margin:0;font-size:13px;color:#8A766C">Aucune demande en attente.</p>
        @endforelse
    </section>

    {{-- Modale de programmation --}}
    @if ($audienceAProgrammer)
        @php($audienceCiblee = $aPlacer->firstWhere('id', $audienceAProgrammer))

        <div style="position:fixed;inset:0;background:rgba(42,26,20,.45);display:flex;align-items:center;justify-content:center;z-index:50;padding:16px">
            <div style="background:#FFFFFF;border-radius:12px;padding:24px 26px;max-width:440px;width:90%">
                <h2 style="font-family:'Playfair Display',serif;font-size:21px;font-weight:700;margin:0 0 6px;color:#2A1A14">Programmer l'audience</h2>

                <div style="font-size:13px;color:#5A463D;margin-bottom:18px">
                    {{ $audienceCiblee->demandeur_nom }} · {{ $audienceCiblee->objet }}
                </div>

                <label style="display:flex;flex-direction:column;gap:6px">
                    <span style="font-size:13px;font-weight:700;color:#2A1A14">Date et heure</span>
                    <input type="datetime-local" wire:model="creneauChoisi"
                           style="width:100%;box-sizing:border-box;padding:10px 12px;border:1px solid rgba(42,26,20,.22);border-radius:7px;font-size:13px;font-family:inherit;color:#2A1A14;background:#FFFFFF">
                </label>

                @error('creneauChoisi')
                    <div style="font-size:12px;color:#B4620A;margin-top:8px">{{ $message }}</div>
                @enderror

                <div style="display:flex;justify-content:flex-end;align-items:center;gap:10px;margin-top:22px;flex-wrap:wrap">
                    <button type="button" wire:click="fermerProgrammation"
                            style="padding:9px 18px;border-radius:999px;border:1px solid rgba(42,26,20,.22);background:#FFFFFF;color:#5A463D;font-size:13px;font-weight:700;cursor:pointer">
                        Annuler
                    </button>

                    <button type="button" wire:click="confirmerProgrammation"
                            style="padding:9px 18px;border-radius:999px;border:none;background:#2D5A27;color:#FFFFFF;font-size:13px;font-weight:700;cursor:pointer">
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
