<?php

namespace App\Livewire\Secretaire;

use App\Models\Audience;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.agenda')]
class ModifierAudience extends Component
{
    use WithFileUploads;

    public Audience $audience;

    public string $nom = '';

    public string $fonction = '';

    public string $organisation = '';

    public string $telephone = '';

    public string $email = '';

    public string $objet = '';

    public string $motif = '';

    public string $recueLe = '';

    public ?TemporaryUploadedFile $lettre = null;

    public function mount(Audience $audience): void
    {
        $this->audience = $audience;

        $this->nom = $audience->demandeur_nom;
        $this->fonction = $audience->demandeur_fonction ?? '';
        $this->organisation = $audience->demandeur_organisation ?? '';
        $this->telephone = $audience->demandeur_telephone ?? '';
        $this->email = $audience->demandeur_email ?? '';
        $this->objet = $audience->objet;
        $this->motif = $audience->motif ?? '';
        $this->recueLe = $audience->recue_le->format('Y-m-d');
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'fonction' => ['nullable', 'string', 'max:255'],
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

    public function enregistrer()
    {
        $this->validate();

        $donnees = [
            'demandeur_nom' => $this->nom,
            'demandeur_fonction' => $this->fonction ?: null,
            'demandeur_organisation' => $this->organisation ?: null,
            'demandeur_telephone' => $this->telephone ?: null,
            'demandeur_email' => $this->email ?: null,
            'objet' => $this->objet,
            'motif' => $this->motif ?: null,
            'recue_le' => $this->recueLe,
        ];

        // Sans nouveau fichier, la lettre existante est conservée telle quelle
        if ($this->lettre !== null) {
            $donnees['lettre_scannee'] = $this->lettre->store('lettres', 'public');
        }

        $this->audience->update($donnees);

        $this->audience->evenements()->create([
            'type' => 'modification',
            'libelle' => 'Demande modifiée',
            'auteur_id' => auth()->id(),
        ]);

        session()->flash('message', 'Demande modifiée.');

        return $this->redirect(route('audience.fiche', $this->audience));
    }

    public function render()
    {
        return view('livewire.secretaire.modifier-audience');
    }
}
