<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audience_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // saisie, programmation, validation, refus, report
            $table->string('libelle');
            $table->text('detail')->nullable();
            $table->foreignId('auteur_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
