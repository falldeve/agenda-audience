<?php

namespace App\Actions;

use App\Models\Audience;

class ValiderAudience
{
    public function execute(Audience $audience): Audience
    {
        $audience->update(['statut' => 'validee']);

        $audience->evenements()->create([
            'type' => 'validation',
            'libelle' => 'Validée par le directeur',
            'auteur_id' => auth()->id(),
        ]);

        return $audience->refresh();
    }
}
