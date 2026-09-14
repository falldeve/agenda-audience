<?php

namespace App\Actions;

use App\Models\Audience;

class RefuserAudience
{
    public function execute(Audience $audience, ?string $motif = null): Audience
    {
        $audience->update([
            'statut' => 'refusee',
            'motif_refus' => $motif,
            'creneau' => null, // le créneau se libère
        ]);

        $audience->evenements()->create([
            'type' => 'refus',
            'libelle' => 'Refusée',
            'detail' => $motif,
            'auteur_id' => auth()->id(),
        ]);

        return $audience->refresh();
    }
}
