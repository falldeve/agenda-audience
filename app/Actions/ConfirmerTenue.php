<?php

namespace App\Actions;

use App\Models\Audience;

class ConfirmerTenue
{
    public function execute(Audience $audience): Audience
    {
        $audience->update(['statut' => 'tenue']);

        $audience->evenements()->create([
            'type' => 'tenue',
            'libelle' => 'Audience tenue',
            'auteur_id' => auth()->id(),
        ]);

        return $audience->refresh();
    }
}
