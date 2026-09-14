<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
