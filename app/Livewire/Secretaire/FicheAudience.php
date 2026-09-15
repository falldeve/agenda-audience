<?php

namespace App\Livewire\Secretaire;

use App\Actions\AnnulerAudience;
use App\Actions\ConfirmerTenue;
use App\Actions\MarquerNonHonoree;
use App\Models\Audience;
use Livewire\Component;

class FicheAudience extends Component
{
    public Audience $audience;

    public bool $confirmationAnnulation = false;

    public string $motifAnnulation = '';

    public bool $confirmationNonHonoree = false;

    public string $motifNonHonoree = '';

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

    public function confirmerTenue(ConfirmerTenue $action): void
    {
        $this->exigerSuiteEchue();

        $action->execute($this->audience);

        session()->flash('message', 'Audience marquée comme tenue.');
    }

    public function ouvrirNonHonoree(): void
    {
        $this->exigerSuiteEchue();

        $this->confirmationNonHonoree = true;
        $this->motifNonHonoree = '';
    }

    public function fermerNonHonoree(): void
    {
        $this->confirmationNonHonoree = false;
    }

    public function confirmerNonHonoree(MarquerNonHonoree $action): void
    {
        $this->exigerSuiteEchue();

        $action->execute($this->audience, $this->motifNonHonoree ?: null);

        $this->confirmationNonHonoree = false;
        $this->motifNonHonoree = '';

        session()->flash('message', 'Audience marquée comme non honorée.');
    }

    /**
     * Les boutons de suite ne sont rendus que pour la secrétaire et sur une audience échue ;
     * la même règle est rejouée côté serveur, un appel Livewire ne passant pas par le rendu.
     */
    private function exigerSuiteEchue(): void
    {
        abort_unless(
            auth()->user()?->role === 'secretaire' && $this->audience->statut === 'echue',
            403
        );
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
