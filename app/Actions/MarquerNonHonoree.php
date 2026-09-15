<?php

namespace App\Actions;

use App\Models\Audience;

class MarquerNonHonoree
{
    public function execute(Audience $audience, ?string $motif = null): Audience
    {
        $audience->update(['statut' => 'non_honoree']);

        $audience->evenements()->create([
            'type' => 'non_honoree',
            'libelle' => 'Demandeur non présenté',
            'detail' => $motif,
            'auteur_id' => auth()->id(),
        ]);

        return $audience->refresh();
    }
}
