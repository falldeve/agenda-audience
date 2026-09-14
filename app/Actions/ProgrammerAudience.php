<?php

namespace App\Actions;

use App\Models\Audience;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class ProgrammerAudience
{
    public function execute(Audience $audience, string $creneau): Audience
    {
        $creneau = Carbon::parse($creneau);

        if ($creneau->isPast()) {
            throw ValidationException::withMessages([
                'creneau' => 'Une audience ne peut pas être programmée dans le passé.',
            ]);
        }

        if ($creneau->isWeekend()) {
            throw ValidationException::withMessages([
                'creneau' => 'Les audiences se tiennent du lundi au vendredi.',
            ]);
        }

        if ($creneau->hour < 9 || $creneau->hour >= 17) {
            throw ValidationException::withMessages([
                'creneau' => 'Les audiences se tiennent entre 9h et 16h.',
            ]);
        }

        // Un créneau déjà occupé par une autre audience active ?
        $conflit = Audience::where('id', '!=', $audience->id)
            ->where('creneau', $creneau)
            ->whereIn('statut', ['programmee', 'validee'])
            ->exists();

        if ($conflit) {
            throw ValidationException::withMessages([
                'creneau' => 'Ce créneau est déjà occupé par une autre audience.',
            ]);
        }

        $audience->update([
            'creneau' => $creneau,
            'statut' => 'programmee',
        ]);

        $audience->evenements()->create([
            'type' => 'programmation',
            'libelle' => 'Audience programmée',
            'detail' => 'Créneau fixé au '.$creneau->format('d/m/Y').' à '.$creneau->format('H:i'),
            'auteur_id' => auth()->id(),
        ]);

        return $audience->refresh();
    }
}
