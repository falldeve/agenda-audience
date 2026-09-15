<?php

namespace App\Console\Commands;

use App\Models\Audience;
use Illuminate\Console\Command;

class MarquerAudiencesEchues extends Command
{
    protected $signature = 'audiences:marquer-echues';

    protected $description = "Passe au statut 'echue' les audiences validées dont le créneau est passé";

    public function handle(): int
    {
        // Seul un fait vérifiable est enregistré ici : la date est passée. Savoir si l'audience
        // s'est réellement tenue relève d'une confirmation humaine depuis la fiche.
        // Seules les audiences encore 'validee' sont reprises : une fois passées en 'echue',
        // elles sortent du périmètre, ce qui rend la commande relançable sans effet de bord.
        $echues = Audience::where('statut', 'validee')
            ->whereNotNull('creneau')
            ->where('creneau', '<', now())
            ->orderBy('creneau')
            ->get();

        if ($echues->isEmpty()) {
            $this->info('Aucune audience validée échue. Rien à faire.');

            return self::SUCCESS;
        }

        foreach ($echues as $audience) {
            $audience->update(['statut' => 'echue']);

            $audience->evenements()->create([
                'type' => 'echeance',
                'libelle' => 'Créneau échu',
                'detail' => 'Créneau du '.$audience->creneau->format('d/m/Y').' à '.$audience->creneau->format('H\hi'),
            ]);

            $this->line("  {$audience->numeroDossier()}  {$audience->demandeur_nom}");
        }

        $this->info("{$echues->count()} audience(s) marquée(s) comme échue(s).");

        return self::SUCCESS;
    }
}
