<?php

namespace App\Actions;

use App\Models\Audience;

class AnnulerAudience
{
    public function execute(Audience $audience, ?string $motif = null): Audience
    {
        $audience->update([
            'statut' => 'annulee',
            'creneau' => null, // le créneau se libère
        ]);

        $audience->evenements()->create([
            'type' => 'annulation',
            'libelle' => 'Demande annulée',
            'detail' => $motif,
            'auteur_id' => auth()->id(),
        ]);

        return $audience->refresh();
    }
}
