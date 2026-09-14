<div>

    <div style="margin-bottom:20px">
        <a href="{{ route('audience.fiche', $audience) }}"
           style="display:inline-block;font-size:13px;font-weight:700;color:#2D5A27;text-decoration:none;margin-bottom:14px">
            ← Retour à la fiche
        </a>

        <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;margin:0 0 6px;color:#2A1A14">Modifier la demande</h1>
        <div style="font-size:14px;color:#5A463D">{{ $audience->numeroDossier() }} · reçue le {{ $audience->recue_le->format('d/m/Y') }}</div>
    </div>

    <div style="background:#FFFFFF;border:1px solid rgba(42,26,20,.12);border-radius:10px;padding:26px 28px">

        {{-- Demandeur --}}
        <section>
            <div style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#2D5A27;font-weight:700;margin-bottom:14px">Demandeur</div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px">
                <label style="display:flex;flex-direction:column;gap:6px">
                    <span style="font-size:13px;font-weight:700;color:#2A1A14">Nom et prénom</span>
                    <input type="text" wire:model="nom"
                           style="padding:10px 12px;border:1px solid rgba(42,26,20,.22);border-radius:7px;font-size:13px;font-family:inherit;color:#2A1A14;background:#FFFFFF">
                    @error('nom')
                        <span style="font-size:12px;color:#B4620A">{{ $message }}</span>
                    @enderror
                </label>

                <label style="display:flex;flex-direction:column;gap:6px">
                    <span style="font-size:13px;font-weight:700;color:#2A1A14">Fonction</span>
                    <input type="text" wire:model="fonction"
                           style="padding:10px 12px;border:1px solid rgba(42,26,20,.22);border-radius:7px;font-size:13px;font-family:inherit;color:#2A1A14;background:#FFFFFF">
                    @error('fonction')
                        <span style="font-size:12px;color:#B4620A">{{ $message }}</span>
                    @enderror
                </label>

                <label style="display:flex;flex-direction:column;gap:6px">
                    <span style="font-size:13px;font-weight:700;color:#2A1A14">Organisation</span>
                    <input type="text" wire:model="organisation"
                           style="padding:10px 12px;border:1px solid rgba(42,26,20,.22);border-radius:7px;font-size:13px;font-family:inherit;color:#2A1A14;background:#FFFFFF">
                    @error('organisation')
                        <span style="font-size:12px;color:#B4620A">{{ $message }}</span>
                    @enderror
                </label>

                <label style="display:flex;flex-direction:column;gap:6px">
                    <span style="font-size:13px;font-weight:700;color:#2A1A14">Téléphone</span>
                    <input type="text" wire:model="telephone"
                           style="padding:10px 12px;border:1px solid rgba(42,26,20,.22);border-radius:7px;font-size:13px;font-family:inherit;color:#2A1A14;background:#FFFFFF">
                    @error('telephone')
                        <span style="font-size:12px;color:#B4620A">{{ $message }}</span>
                    @enderror
                </label>

                <label style="display:flex;flex-direction:column;gap:6px">
                    <span style="font-size:13px;font-weight:700;color:#2A1A14">Adresse électronique</span>
                    <input type="email" wire:model="email"
                           style="padding:10px 12px;border:1px solid rgba(42,26,20,.22);border-radius:7px;font-size:13px;font-family:inherit;color:#2A1A14;background:#FFFFFF">
                    @error('email')
                        <span style="font-size:12px;color:#B4620A">{{ $message }}</span>
                    @enderror
                </label>
            </div>
        </section>

        {{-- Objet de la demande --}}
        <section style="margin-top:26px;padding-top:24px;border-top:1px solid rgba(42,26,20,.12)">
            <div style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#2D5A27;font-weight:700;margin-bottom:14px">Objet de la demande</div>

            <div style="display:flex;flex-direction:column;gap:16px">
                <label style="display:flex;flex-direction:column;gap:6px">
                    <span style="font-size:13px;font-weight:700;color:#2A1A14">Objet</span>
                    <input type="text" wire:model="objet"
                           style="padding:10px 12px;border:1px solid rgba(42,26,20,.22);border-radius:7px;font-size:13px;font-family:inherit;color:#2A1A14;background:#FFFFFF">
                    @error('objet')
                        <span style="font-size:12px;color:#B4620A">{{ $message }}</span>
                    @enderror
                </label>

                <label style="display:flex;flex-direction:column;gap:6px">
                    <span style="font-size:13px;font-weight:700;color:#2A1A14">Motif détaillé</span>
                    <textarea wire:model="motif" rows="5"
                              style="padding:10px 12px;border:1px solid rgba(42,26,20,.22);border-radius:7px;font-size:13px;font-family:inherit;color:#2A1A14;background:#FFFFFF;resize:vertical"></textarea>
                    @error('motif')
                        <span style="font-size:12px;color:#B4620A">{{ $message }}</span>
                    @enderror
                </label>

                <label style="display:flex;flex-direction:column;gap:6px;max-width:260px">
                    <span style="font-size:13px;font-weight:700;color:#2A1A14">Date de réception du courrier</span>
                    <input type="date" wire:model="recueLe"
                           style="padding:10px 12px;border:1px solid rgba(42,26,20,.22);border-radius:7px;font-size:13px;font-family:inherit;color:#2A1A14;background:#FFFFFF">
                    @error('recueLe')
                        <span style="font-size:12px;color:#B4620A">{{ $message }}</span>
                    @enderror
                </label>
            </div>
        </section>

        {{-- Lettre scannée --}}
        <section style="margin-top:26px;padding-top:24px;border-top:1px solid rgba(42,26,20,.12)">
            <div style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#2D5A27;font-weight:700;margin-bottom:14px">Lettre scannée</div>

            @if ($audience->lettre_scannee)
                <div style="font-size:13px;color:#5A463D;margin-bottom:12px">
                    Lettre actuelle :
                    <a href="{{ asset('storage/'.$audience->lettre_scannee) }}" target="_blank" rel="noopener"
                       style="color:#2D5A27;font-weight:700;text-decoration:none">{{ basename($audience->lettre_scannee) }}</a>
                </div>
            @endif

            <label style="display:flex;align-items:center;gap:16px;border:1.5px dashed rgba(42,26,20,.28);background:#F7F5F0;border-radius:9px;padding:20px 22px;cursor:pointer">
                <div style="width:44px;height:44px;flex:0 0 44px;border-radius:8px;background:#FFFFFF;border:1px solid rgba(42,26,20,.12);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#2D5A27;letter-spacing:.04em">PDF</div>

                <div style="min-width:0">
                    <div style="font-size:14px;font-weight:700;color:#2A1A14">{{ $audience->lettre_scannee ? 'Remplacer la lettre scannée' : 'Importer la lettre scannée' }}</div>
                    <div style="font-size:12px;color:#8A766C;margin-top:2px">PDF, JPG ou PNG · 10 Mo maximum</div>

                    @if ($lettre)
                        <div style="font-size:12px;color:#2D5A27;font-weight:700;margin-top:6px">{{ $lettre->getClientOriginalName() }}</div>
                    @endif

                    <div wire:loading wire:target="lettre" style="font-size:12px;color:#5A463D;margin-top:6px">Import en cours…</div>
                </div>

                <input type="file" wire:model="lettre" style="display:none">
            </label>

            @error('lettre')
                <div style="font-size:12px;color:#B4620A;margin-top:8px">{{ $message }}</div>
            @enderror
        </section>

        {{-- Actions --}}
        <div style="margin-top:26px;padding-top:22px;border-top:1px solid rgba(42,26,20,.12);display:flex;justify-content:flex-end;align-items:center;gap:10px;flex-wrap:wrap">
            <a href="{{ route('audience.fiche', $audience) }}"
               style="padding:9px 18px;border-radius:999px;border:1px solid rgba(42,26,20,.22);background:#FFFFFF;color:#5A463D;font-size:13px;font-weight:700;text-decoration:none">
                Annuler
            </a>

            <button type="button" wire:click="enregistrer"
                    wire:loading.attr="disabled" wire:target="enregistrer,lettre"
                    style="padding:9px 18px;border-radius:999px;border:none;background:#2D5A27;color:#FFFFFF;font-size:13px;font-weight:700;cursor:pointer">
                <span wire:loading.remove wire:target="enregistrer,lettre">Enregistrer les modifications</span>
                <span wire:loading wire:target="lettre">Import en cours…</span>
                <span wire:loading wire:target="enregistrer">Enregistrement…</span>
            </button>
        </div>

    </div>

</div>
