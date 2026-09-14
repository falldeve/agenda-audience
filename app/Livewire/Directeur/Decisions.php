<?php

namespace App\Livewire\Directeur;

use App\Actions\RefuserAudience;
use App\Actions\ValiderAudience;
use App\Models\Audience;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.directeur')]
class Decisions extends Component
{
    public ?int $audienceARefuser = null;
    public string $motifRefus = '';

    public function valider(int $id, ValiderAudience $action)
    {
        $action->execute(Audience::findOrFail($id));
        session()->flash('message', 'Audience validée.');
    }

    public function ouvrirRefus(int $id)
    {
        $this->audienceARefuser = $id;
        $this->motifRefus = '';
    }

    public function fermerRefus()
    {
        $this->audienceARefuser = null;
        $this->motifRefus = '';
    }

    public function confirmerRefus(RefuserAudience $action)
    {
        $audience = Audience::findOrFail($this->audienceARefuser);
        $action->execute($audience, $this->motifRefus ?: null);

        $this->fermerRefus();
        session()->flash('message', 'Audience refusée.');
    }

    public function render()
    {
        return view('livewire.directeur.decisions', [
            'audiences' => Audience::where('statut', 'programmee')
                ->orderBy('creneau')
                ->get(),
        ]);
    }
}