<?php

namespace Database\Seeders;

use App\Models\Audience;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;

class AudienceSeeder extends Seeder
{
    public function run(): void
    {
        // Les deux comptes de travail
        $secretaire = User::factory()->create([
            'name' => 'Fatou Ndiaye',
            'email' => 'secretaire@dg.sn',
            'role' => 'secretaire',
        ]);

        $directeur = User::factory()->create([
            'name' => 'Le Directeur',
            'email' => 'directeur@dg.sn',
            'role' => 'directeur',
        ]);

        // Demandes à placer (sans créneau)
        $aPlacer = [
            ['Ousmane Fall', 'Chambre de commerce', 'Président', 'Demande de financement'],
            ['Aïssatou Ndiaye', 'Association Takku', 'Coordinatrice', 'Présentation projet'],
            ['Ibrahima Sow', 'Préfecture', 'Préfet adjoint', 'Coordination régionale'],
        ];
        foreach ($aPlacer as [$nom, $org, $fonction, $objet]) {
            $audience = Audience::create([
                'demandeur_nom' => $nom,
                'demandeur_organisation' => $org,
                'demandeur_fonction' => $fonction,
                'objet' => $objet,
                'recue_le' => now()->subDays(rand(1, 5)),
                'statut' => 'en_attente',
                'saisie_par' => $secretaire->id,
            ]);

            $this->evenement(
                $audience,
                'saisie',
                'Saisie de la demande',
                'Courrier enregistré par '.$secretaire->name,
                $secretaire->id,
                $audience->recue_le->copy()->setTime(9, 0),
            );
        }

        // Audiences programmées et validées cette semaine (lundi -> vendredi, heures pleines 9h-16h)
        $lundi = now()->startOfWeek();

        $planifiees = [
            ['Amadou Diop', 'Ministère du Budget', 'Directeur de cabinet', 'Présentation budgétaire', $lundi->copy()->setTime(9, 0), 'validee'],
            ['Mariama Fall', 'ONG Jappoo', 'Présidente', 'Convention femmes', $lundi->copy()->addDays(1)->setTime(10, 0), 'validee'],
            ['Moussa Ba', 'Société de transport', 'Gérant', 'Tarification', $lundi->copy()->addDays(2)->setTime(11, 0), 'programmee'],
            ['Seynabou Diop', 'Mairie de Guédiawaye', 'Adjointe au maire', 'Jumelage', $lundi->copy()->addDays(3)->setTime(14, 0), 'programmee'],
        ];
        foreach ($planifiees as [$nom, $org, $fonction, $objet, $creneau, $statut]) {
            // Le courrier précède le créneau, sinon l'historique programmerait une audience déjà passée
            $recueLe = $creneau->copy()->subDays(rand(5, 9));

            $audience = Audience::create([
                'demandeur_nom' => $nom,
                'demandeur_organisation' => $org,
                'demandeur_fonction' => $fonction,
                'objet' => $objet,
                'recue_le' => $recueLe,
                'creneau' => $creneau,
                'statut' => $statut,
                'saisie_par' => $secretaire->id,
            ]);

            $saisieLe = $recueLe->copy()->setTime(9, 0);

            $this->evenement(
                $audience,
                'saisie',
                'Saisie de la demande',
                'Courrier enregistré par '.$secretaire->name,
                $secretaire->id,
                $saisieLe,
            );

            $this->evenement(
                $audience,
                'programmation',
                'Audience programmée',
                'Créneau fixé au '.$creneau->format('d/m/Y').' à '.$creneau->format('H:i'),
                $secretaire->id,
                $saisieLe->copy()->addDay()->setTime(11, 0),
            );

            if ($statut === 'validee') {
                $this->evenement(
                    $audience,
                    'validation',
                    'Validée par le directeur',
                    null,
                    $directeur->id,
                    $saisieLe->copy()->addDays(2)->setTime(15, 0),
                );
            }
        }
    }

    /**
     * Les timestamps sont forcés pour que l'historique de démo se lise dans l'ordre.
     */
    private function evenement(
        Audience $audience,
        string $type,
        string $libelle,
        ?string $detail,
        int $auteurId,
        CarbonInterface $survenuLe,
    ): void {
        $audience->evenements()->create([
            'type' => $type,
            'libelle' => $libelle,
            'detail' => $detail,
            'auteur_id' => $auteurId,
        ])->forceFill([
            'created_at' => $survenuLe,
            'updated_at' => $survenuLe,
        ])->save();
    }
}
