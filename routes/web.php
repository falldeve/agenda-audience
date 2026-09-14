<?php

use App\Livewire\Secretaire\Agenda;
use App\Livewire\Secretaire\FicheAudience;
use App\Livewire\Secretaire\ModifierAudience;
use App\Livewire\Secretaire\NouvelleDemande;
use Illuminate\Support\Facades\Route;
use App\Livewire\Directeur\Decisions;
use App\Livewire\Directeur\AudiencesDuJour;

Route::view('/', 'welcome')->name('home');





// Espace secrétaire
Route::middleware(['auth', 'role:secretaire'])->group(function () {
    Route::get('/agenda', Agenda::class)->name('agenda');
    Route::get('/nouvelle-demande', NouvelleDemande::class)->name('nouvelle-demande');
    Route::get('/audience/{audience}/modifier', ModifierAudience::class)->name('audience.modifier');
});

// Espace directeur
Route::middleware(['auth', 'role:directeur'])->group(function () {
    Route::get('/directeur/audiences', AudiencesDuJour::class)->name('directeur.audiences');
    Route::get('/directeur/decisions', Decisions::class)->name('directeur.decisions');
});

// Fiche audience : les deux rôles
Route::middleware(['auth', 'role:secretaire,directeur'])->group(function () {
    Route::get('/audience/{audience}', FicheAudience::class)->name('audience.fiche');
});


require __DIR__.'/settings.php';