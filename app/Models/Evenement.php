<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evenement extends Model
{
    protected $fillable = [
        'audience_id', 'type', 'libelle', 'detail', 'auteur_id',
    ];

    public function audience(): BelongsTo
    {
        return $this->belongsTo(Audience::class);
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }
}
