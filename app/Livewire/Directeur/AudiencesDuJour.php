<?php

namespace App\Livewire\Directeur;

use App\Models\Audience;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.directeur')]
class AudiencesDuJour extends Component
{
    public string $vue = 'jour';

    public function voirJour()
    {
        $this->vue = 'jour';
    }

    public function voirSemaine()
    {
        $this->vue = 'semaine';
    }

    public function render()
    {
        $requete = Audience::whereIn('statut', ['programmee', 'validee', 'echue', 'tenue', 'non_honoree'])
            ->whereNotNull('creneau')
            ->orderBy('creneau');

        if ($this->vue === 'jour') {
            $requete->whereDate('creneau', now()->toDateString());
            $libelle = now()->translatedFormat('l j F Y');
        } else {
            $debut = now()->startOfWeek();
            $fin = now()->startOfWeek()->addDays(4)->endOfDay();
            $requete->whereBetween('creneau', [$debut, $fin]);
            $libelle = $debut->translatedFormat('j').' — '.$debut->copy()->addDays(4)->translatedFormat('j F Y');
        }

        return view('livewire.directeur.audiences-du-jour', [
            'audiences' => $requete->get(),
            'libelle' => $libelle,
        ]);
    }
}
