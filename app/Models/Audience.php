<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Valeurs possibles de la colonne `statut` (chaîne libre en base, pas d'enum) :
 *
 * - en_attente   demande reçue, pas encore de créneau
 * - programmee   créneau posé par la secrétaire, en attente de décision du directeur
 * - validee      créneau accepté par le directeur
 * - refusee      demande refusée par le directeur
 * - reportee     créneau retiré, à replacer
 * - annulee      demande annulée, conservée pour la traçabilité
 * - echue        créneau validé désormais passé — seul fait établi automatiquement
 * - tenue        l'audience a bien eu lieu, confirmé à la main depuis la fiche
 * - non_honoree  le demandeur ne s'est pas présenté, constaté à la main depuis la fiche
 */
class Audience extends Model
{
    protected $fillable = [
        'demandeur_nom', 'demandeur_organisation', 'demandeur_fonction',
        'demandeur_telephone', 'demandeur_email',
        'objet', 'motif', 'recue_le', 'lettre_scannee',
        'creneau', 'statut', 'motif_refus', 'saisie_par',
    ];

    protected function casts(): array
    {
        return [
            'recue_le' => 'date',
            'creneau' => 'datetime',
        ];
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'saisie_par');
    }

    public function evenements(): HasMany
    {
        return $this->hasMany(Evenement::class)->orderBy('created_at');
    }

    public function estProgrammee(): bool
    {
        return $this->creneau !== null;
    }

    public function numeroDossier(): string
    {
        return 'AUD-'.$this->created_at->year.'-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function scopeAPlacer(Builder $query): Builder
    {
        return $query->whereNull('creneau')->whereIn('statut', ['en_attente', 'reportee']);
    }

    public function scopeDuJour(Builder $query, ?string $date = null): Builder
    {
        return $query->whereDate('creneau', $date ?? now()->toDateString());
    }
}
