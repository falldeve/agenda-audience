<?php

namespace App\Livewire\Secretaire;

use App\Actions\ProgrammerAudience;
use App\Models\Audience;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.agenda')]
class Agenda extends Component
{
    // Audience dont la modale de programmation est ouverte (null = modale fermée)
    public ?int $audienceAProgrammer = null;

    // Créneau saisi dans la modale
    public string $creneauChoisi = '';

    // Nombre de semaines d'écart avec la semaine courante (négatif = passé)
    public int $decalageSemaine = 0;

    public function semainePrecedente(): void
    {
        $this->decalageSemaine--;
    }

    public function semaineSuivante(): void
    {
        $this->decalageSemaine++;
    }

    public function semaineActuelle(): void
    {
        $this->decalageSemaine = 0;
    }

    public function ouvrirProgrammation(int $audienceId): void
    {
        $this->audienceAProgrammer = $audienceId;
        $this->creneauChoisi = '';
        $this->resetErrorBag();
    }

    public function fermerProgrammation(): void
    {
        $this->audienceAProgrammer = null;
    }

    public function confirmerProgrammation(ProgrammerAudience $action): void
    {
        $this->validate(
            ['creneauChoisi' => 'required|date'],
            ['required' => 'Indiquez une date et une heure.', 'date' => 'Format de date invalide.']
        );

        $audience = Audience::findOrFail($this->audienceAProgrammer);

        try {
            $action->execute($audience, $this->creneauChoisi);
        } catch (ValidationException $e) {
            // Conflit, week-end ou hors horaires : on garde la modale ouverte pour correction
            $this->addError('creneauChoisi', $e->errors()['creneau'][0]);

            return;
        }

        session()->flash('message', 'Audience programmée.');
        $this->audienceAProgrammer = null;
    }

    public function render()
    {
        app()->setLocale('fr');

        $debutSemaine = now()->startOfWeek()->addWeeks($this->decalageSemaine);

        $parCase = Audience::whereNotNull('creneau')
            ->whereIn('statut', ['programmee', 'validee', 'echue', 'tenue', 'non_honoree'])
            ->get()
            ->groupBy(fn (Audience $audience) => $audience->creneau->format('Y-m-d-G'));

        $jours = collect(range(0, 4))->map(fn (int $i) => $debutSemaine->copy()->addDays($i));
        $heures = range(9, 16);

        return view('livewire.secretaire.agenda', [
            'jours' => $jours,
            'heures' => $heures,
            'aPlacer' => Audience::aPlacer()->orderBy('recue_le')->get(),
            'parCase' => $parCase,
            'nbAudiencesSemaine' => $jours->crossJoin($heures)->sum(
                fn (array $case) => ($parCase[$case[0]->format('Y-m-d').'-'.$case[1]] ?? collect())->count()
            ),
        ]);
    }
}
