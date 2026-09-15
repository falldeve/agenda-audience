<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audiences', function (Blueprint $table) {
            $table->id();
            // Le demandeur (pas un utilisateur, juste des infos)
            $table->string('demandeur_nom');
            $table->string('demandeur_organisation')->nullable();
            $table->string('demandeur_telephone')->nullable();
            $table->string('demandeur_email')->nullable();
            // La demande
            $table->string('objet');
            $table->text('motif')->nullable();
            $table->date('recue_le');
            $table->string('lettre_scannee')->nullable(); // chemin du fichier
            // L'agenda
            $table->dateTime('creneau')->nullable();       // null = pas encore programmée
            // Le workflow
            $table->string('statut')->default('en_attente'); // valeurs possibles documentées sur le modèle Audience
            $table->text('motif_refus')->nullable();
            $table->foreignId('saisie_par')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audiences');
    }
};
