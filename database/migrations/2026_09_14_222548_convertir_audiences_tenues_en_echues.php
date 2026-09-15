<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Les audiences passées en 'tenue' par l'ancienne commande automatique affirmaient un fait
     * que l'application ne pouvait pas connaître. Elles redeviennent 'echue' : seule la date est
     * établie. Leur historique est conservé — l'événement existant est requalifié, pas supprimé.
     *
     * Le repère est l'absence d'auteur : une confirmation humaine porte toujours un auteur_id,
     * une bascule automatique jamais. Les tenues confirmées à la main sont donc épargnées.
     */
    public function up(): void
    {
        $automatiques = DB::table('evenements')
            ->join('audiences', 'audiences.id', '=', 'evenements.audience_id')
            ->where('audiences.statut', 'tenue')
            ->where('evenements.type', 'tenue')
            ->whereNull('evenements.auteur_id')
            ->pluck('evenements.id', 'evenements.audience_id');

        if ($automatiques->isEmpty()) {
            return;
        }

        DB::table('audiences')
            ->whereIn('id', $automatiques->keys())
            ->update(['statut' => 'echue']);

        DB::table('evenements')
            ->whereIn('id', $automatiques->values())
            ->update(['type' => 'echeance', 'libelle' => 'Créneau échu']);
    }

    public function down(): void
    {
        $automatiques = DB::table('evenements')
            ->join('audiences', 'audiences.id', '=', 'evenements.audience_id')
            ->where('audiences.statut', 'echue')
            ->where('evenements.type', 'echeance')
            ->whereNull('evenements.auteur_id')
            ->pluck('evenements.id', 'evenements.audience_id');

        if ($automatiques->isEmpty()) {
            return;
        }

        DB::table('audiences')
            ->whereIn('id', $automatiques->keys())
            ->update(['statut' => 'tenue']);

        DB::table('evenements')
            ->whereIn('id', $automatiques->values())
            ->update(['type' => 'tenue', 'libelle' => 'Audience tenue']);
    }
};
