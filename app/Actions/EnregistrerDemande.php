<?php

namespace App\Actions;

use App\Models\Audience;

class EnregistrerDemande
{
    /**
     * @param  array{
     *     demandeur_nom: string,
     *     demandeur_organisation: ?string,
     *     demandeur_telephone: ?string,
     *     demandeur_email: ?string,
     *     objet: string,
     *     motif: ?string,
     *     recue_le: string,
     * }  $donnees
     */
    public function execute(array $donnees, ?string $lettreScannee = null): Audience
    {
        $auteur = auth()->user();

        $audience = Audience::create([
            ...$donnees,
            'lettre_scannee' => $lettreScannee,
            'creneau' => null,
            'statut' => 'en_attente',
            'saisie_par' => $auteur?->id,
        ]);

        $audience->evenements()->create([
            'type' => 'saisie',
            'libelle' => 'Saisie de la demande',
            'detail' => $auteur === null ? null : 'Courrier enregistré par '.$auteur->name,
            'auteur_id' => $auteur?->id,
        ]);

        return $audience;
    }
}
