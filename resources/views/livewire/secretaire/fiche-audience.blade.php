<div>

    <style>
        .fiche-colonnes {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .fiche-colonnes {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php($estDirecteur = auth()->user()->role === 'directeur')

    @php($statut = match ($audience->statut) {
        'validee' => ['Validée', '#2D5A27', '#EAF0E6'],
        'programmee' => ['Programmée', '#1F4E79', '#E7EEF5'],
        'refusee' => ['Refusée', '#A32A2A', '#F6E3E3'],
        'reportee' => ['Reportée', '#B4620A', '#F6E9DC'],
        'annulee' => ['Annulée', '#5A463D', '#EDE9E3'],
        default => ['En attente', '#1F4E79', '#E7EEF5'],
    })

    @if (session('message'))
        <div style="margin-bottom:16px;padding:12px 16px;border-radius:8px;border-left:3px solid #2D5A27;background:#EAF0E6;color:#2A1A14;font-size:13px;font-weight:700">{{ session('message') }}</div>
    @endif

    {{-- En-tête --}}
    <a href="{{ $estDirecteur ? route('directeur.audiences') : route('agenda') }}"
       style="display:inline-block;font-size:13px;font-weight:700;color:#2D5A27;text-decoration:none;margin-bottom:14px">
        ← Retour {{ $estDirecteur ? 'à mes audiences' : "à l'agenda" }}
    </a>

    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;flex-wrap:wrap;margin-bottom:22px">
        <div style="min-width:0">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px">
                <span style="padding:4px 12px;border-radius:999px;background:{{ $statut[2] }};color:{{ $statut[1] }};font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase">{{ $statut[0] }}</span>
                <span style="font-size:12px;color:#8A766C;font-weight:700;letter-spacing:.04em">{{ $audience->numeroDossier() }}</span>
            </div>

            <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;margin:0 0 6px;color:#2A1A14">{{ $audience->objet }}</h1>

            <div style="font-size:14px;color:#5A463D">
                {{ $audience->demandeur_nom }}@if ($audience->demandeur_organisation) · {{ $audience->demandeur_organisation }}@endif
            </div>
        </div>

        @unless ($audience->statut === 'annulee' || $estDirecteur)
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <a href="{{ route('audience.modifier', $audience) }}"
                   style="padding:9px 18px;border-radius:999px;border:1px solid rgba(42,26,20,.20);background:#FFFFFF;color:#2A1A14;font-size:13px;font-weight:700;text-decoration:none;white-space:nowrap">
                    Modifier
                </a>

                <button type="button" wire:click="ouvrirAnnulation"
                        style="padding:9px 18px;border-radius:999px;border:1px solid rgba(163,44,44,.45);background:#FFFFFF;color:#A32A2A;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap">
                    Annuler la demande
                </button>
            </div>
        @endunless
    </div>

    {{-- Corps en deux colonnes --}}
    <div class="fiche-colonnes">

        <div style="display:flex;flex-direction:column;gap:18px">

            {{-- Demandeur --}}
            <section style="background:#FFFFFF;border:1px solid rgba(42,26,20,.12);border-radius:10px;padding:22px 24px">
                <div style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#2D5A27;font-weight:700;margin-bottom:16px">Demandeur</div>

                @php($infos = array_filter([
                    ['Nom et prénom', $audience->demandeur_nom],
                    ['Fonction', $audience->demandeur_fonction],
                    ['Organisation', $audience->demandeur_organisation],
                    ['Téléphone', $audience->demandeur_telephone],
                    ['Adresse électronique', $audience->demandeur_email],
                    ['Courrier reçu le', $audience->recue_le->format('d/m/Y')],
                    ['Créneau', $audience->creneau
                        ? $audience->creneau->format('d/m/Y').' à '.$audience->creneau->format('H\hi')
                        : 'Non encore programmé'],
                ], fn (array $info) => filled($info[1])))

                <div style="display:flex;flex-direction:column;gap:14px">
                    @foreach ($infos as [$label, $valeur])
                        <div>
                            <div style="font-size:11px;color:#8A766C;font-weight:700;letter-spacing:.04em">{{ $label }}</div>
                            <div style="font-size:14px;color:#2A1A14;margin-top:2px">{{ $valeur }}</div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Motif détaillé --}}
            <section style="background:#FFFFFF;border:1px solid rgba(42,26,20,.12);border-radius:10px;padding:22px 24px">
                <div style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#2D5A27;font-weight:700;margin-bottom:14px">Motif détaillé</div>

                @if (filled($audience->motif))
                    <div style="font-size:14px;color:#2A1A14;line-height:1.6;white-space:pre-line">{{ $audience->motif }}</div>
                @else
                    <div style="font-size:13px;color:#8A766C">Aucun motif renseigné</div>
                @endif
            </section>

        </div>

        {{-- Lettre scannée --}}
        <section style="background:#FFFFFF;border:1px solid rgba(42,26,20,.12);border-radius:10px;padding:22px 24px">
            <div style="display:flex;align-items:baseline;justify-content:space-between;gap:14px;margin-bottom:16px">
                <div style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#2D5A27;font-weight:700">Lettre scannée</div>

                @if ($audience->lettre_scannee)
                    <a href="{{ asset('storage/'.$audience->lettre_scannee) }}" target="_blank" rel="noopener"
                       style="font-size:13px;font-weight:700;color:#2D5A27;text-decoration:none;white-space:nowrap">Ouvrir le PDF</a>
                @endif
            </div>

            @if ($audience->lettre_scannee)
                @php($extension = strtolower(pathinfo($audience->lettre_scannee, PATHINFO_EXTENSION)))

                @if (in_array($extension, ['jpg', 'jpeg', 'png']))
                    <img src="{{ asset('storage/'.$audience->lettre_scannee) }}"
                         alt="Lettre scannée"
                         style="display:block;width:100%;border:1px solid rgba(42,26,20,.12);border-radius:8px">
                @else
                    <div style="display:flex;align-items:center;gap:14px;border:1.5px dashed rgba(42,26,20,.20);background:#F7F5F0;border-radius:9px;padding:20px 22px">
                        <div style="width:44px;height:44px;flex:0 0 44px;border-radius:8px;background:#FFFFFF;border:1px solid rgba(42,26,20,.12);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#2D5A27;letter-spacing:.04em">{{ strtoupper($extension) }}</div>
                        <div style="min-width:0">
                            <div style="font-size:14px;font-weight:700;color:#2A1A14">Document joint</div>
                            <div style="font-size:12px;color:#8A766C;margin-top:2px;overflow-wrap:anywhere">{{ basename($audience->lettre_scannee) }}</div>
                        </div>
                    </div>
                @endif
            @else
                <div style="font-size:13px;color:#8A766C">Aucune lettre jointe</div>
            @endif
        </section>

    </div>

    {{-- Historique --}}
    <section style="margin-top:18px;background:#FFFFFF;border:1px solid rgba(42,26,20,.12);border-radius:10px;padding:22px 24px">
        <div style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#2D5A27;font-weight:700;margin-bottom:18px">Historique du dossier</div>

        @forelse ($audience->evenements as $evenement)
            <div style="display:flex;gap:14px">
                <div style="display:flex;flex-direction:column;align-items:center;flex:0 0 11px">
                    <span style="width:11px;height:11px;border-radius:999px;background:#2D5A27;flex:0 0 11px"></span>

                    @unless ($loop->last)
                        <span style="flex:1;width:1px;background:rgba(42,26,20,.15);margin-top:4px"></span>
                    @endunless
                </div>

                <div style="min-width:0;padding-bottom:{{ $loop->last ? '0' : '20px' }}">
                    <div style="font-size:14px;font-weight:700;color:#2A1A14">{{ $evenement->libelle }}</div>

                    @if (filled($evenement->detail))
                        <div style="font-size:13px;color:#5A463D;margin-top:2px">{{ $evenement->detail }}</div>
                    @endif

                    <div style="font-size:12px;color:#8A766C;margin-top:4px">
                        {{ $evenement->created_at->format('d/m/Y').' à '.$evenement->created_at->format('H\hi') }}@if ($evenement->auteur) · {{ $evenement->auteur->name }}@endif
                    </div>
                </div>
            </div>
        @empty
            <div style="font-size:13px;color:#8A766C">Aucun événement enregistré.</div>
        @endforelse
    </section>

    {{-- Confirmation d'annulation --}}
    @if ($confirmationAnnulation)
        <div style="position:fixed;inset:0;background:rgba(42,26,20,.45);display:flex;align-items:center;justify-content:center;z-index:50;padding:16px">
            <div style="background:#FFFFFF;border-radius:12px;padding:24px 26px;max-width:440px;width:90%">
                <h2 style="font-family:'Playfair Display',serif;font-size:21px;font-weight:700;margin:0 0 6px;color:#2A1A14">Annuler la demande</h2>

                <div style="font-size:13px;color:#5A463D;margin-bottom:18px">
                    {{ $audience->demandeur_nom }} · {{ $audience->objet }}<br>
                    La demande sera conservée dans l'historique, mais retirée de l'agenda.
                </div>

                <label style="display:flex;flex-direction:column;gap:6px">
                    <span style="font-size:13px;font-weight:700;color:#2A1A14">Motif <span style="font-weight:400;color:#8A766C">(facultatif)</span></span>
                    <textarea wire:model="motifAnnulation" rows="3"
                              style="width:100%;box-sizing:border-box;padding:10px 12px;border:1px solid rgba(42,26,20,.22);border-radius:7px;font-size:13px;font-family:inherit;color:#2A1A14;background:#FFFFFF;resize:vertical"></textarea>
                </label>

                <div style="display:flex;justify-content:flex-end;align-items:center;gap:10px;margin-top:22px;flex-wrap:wrap">
                    <button type="button" wire:click="fermerAnnulation"
                            style="padding:9px 18px;border-radius:999px;border:1px solid rgba(42,26,20,.22);background:#FFFFFF;color:#5A463D;font-size:13px;font-weight:700;cursor:pointer">
                        Retour
                    </button>

                    <button type="button" wire:click="confirmerAnnulation"
                            style="padding:9px 18px;border-radius:999px;border:none;background:#A32A2A;color:#FFFFFF;font-size:13px;font-weight:700;cursor:pointer">
                        Confirmer l'annulation
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>