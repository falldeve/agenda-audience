<?php

namespace App\Livewire\Secretaire;

use App\Actions\AnnulerAudience;
use App\Models\Audience;
use Livewire\Component;

class FicheAudience extends Component
{
    public Audience $audience;

    public bool $confirmationAnnulation = false;

    public string $motifAnnulation = '';

    public function mount(Audience $audience): void
    {
        $this->audience = $audience;
    }

    public function ouvrirAnnulation(): void
    {
        $this->confirmationAnnulation = true;
        $this->motifAnnulation = '';
    }

    public function fermerAnnulation(): void
    {
        $this->confirmationAnnulation = false;
    }

    public function confirmerAnnulation(AnnulerAudience $action): void
    {
        $action->execute($this->audience, $this->motifAnnulation ?: null);

        $this->confirmationAnnulation = false;
        $this->motifAnnulation = '';

        session()->flash('message', 'Demande annulée.');
    }

    public function render()
    {
        app()->setLocale('fr');

        // Rechargé ici plutôt que dans mount() : les relations ne survivent pas à l'hydratation
        $this->audience->loadMissing(['auteur', 'evenements.auteur']);

        $layout = request()->user()?->role === 'directeur'
            ? 'layouts.directeur'
            : 'layouts.agenda';

        // layout() est un macro que Livewire greffe sur Illuminate\View\View à l'exécution :
        // l'analyse statique ne peut pas le connaître, d'où la variable volontairement non typée.
        /** @var mixed $vue */
        $vue = view('livewire.secretaire.fiche-audience');

        return $vue->layout($layout);
    }
}