<?php

namespace App\Console\Commands;

use App\Models\Audience;
use Illuminate\Console\Command;

class RetroremplirHistorique extends Command
{
    protected $signature = 'audiences:retroremplir-historique';

    protected $description = "Crée l'événement de saisie manquant sur les audiences antérieures à l'historique";

    public function handle(): int
    {
        $sansHistorique = Audience::doesntHave('evenements')->get();

        if ($sansHistorique->isEmpty()) {
            $this->info('Toutes les audiences ont déjà un historique. Rien à faire.');

            return self::SUCCESS;
        }

        foreach ($sansHistorique as $audience) {
            $audience->evenements()->create([
                'type' => 'saisie',
                'libelle' => 'Saisie de la demande',
                'auteur_id' => $audience->saisie_par,
            ])->forceFill([
                'created_at' => $audience->recue_le,
                'updated_at' => $audience->recue_le,
            ])->save();

            $this->line("  {$audience->numeroDossier()}  {$audience->demandeur_nom}");
        }

        $this->info("{$sansHistorique->count()} événement(s) de saisie créé(s).");

        return self::SUCCESS;
    }
}
