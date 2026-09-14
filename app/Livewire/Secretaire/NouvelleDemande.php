<?php

namespace App\Livewire\Secretaire;

use App\Actions\EnregistrerDemande;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.agenda')]
class NouvelleDemande extends Component
{
    use WithFileUploads;

    public string $nom = '';

    public string $organisation = '';

    public string $telephone = '';

    public string $email = '';

    public string $objet = '';

    public string $motif = '';

    public string $recueLe = '';

    public ?TemporaryUploadedFile $lettre = null;

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'organisation' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'objet' => ['required', 'string', 'max:255'],
            'motif' => ['nullable', 'string'],
            'recueLe' => ['required', 'date'],
            'lettre' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'nom.required' => 'Indiquez le nom du demandeur.',
            'objet.required' => "Indiquez l'objet de la demande.",
            'email.email' => "L'adresse électronique n'est pas valide.",
            'recueLe.required' => 'Indiquez la date de réception du courrier.',
            'recueLe.date' => 'Format de date invalide.',
            'lettre.mimes' => 'La lettre doit être un fichier PDF, JPG ou PNG.',
            'lettre.max' => 'La lettre ne doit pas dépasser 10 Mo.',
        ];
    }

    public function enregistrer(EnregistrerDemande $action)
    {
        $this->validate();

        $chemin = $this->lettre?->store('lettres', 'public');

        $action->execute([
            'demandeur_nom' => $this->nom,
            'demandeur_organisation' => $this->organisation ?: null,
            'demandeur_telephone' => $this->telephone ?: null,
            'demandeur_email' => $this->email ?: null,
            'objet' => $this->objet,
            'motif' => $this->motif ?: null,
            'recue_le' => $this->recueLe,
        ], $chemin);

        session()->flash('message', 'Demande enregistrée.');

        return $this->redirect(route('agenda'));
    }

    public function render()
    {
        return view('livewire.secretaire.nouvelle-demande');
    }
}
