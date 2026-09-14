<?php

use App\Livewire\Secretaire\Agenda;
use App\Livewire\Secretaire\FicheAudience;
use App\Livewire\Secretaire\ModifierAudience;
use App\Livewire\Secretaire\NouvelleDemande;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/agenda', Agenda::class)->name('agenda');
    Route::get('/nouvelle-demande', NouvelleDemande::class)->name('nouvelle-demande');
    Route::get('/audience/{audience}', FicheAudience::class)->name('audience.fiche');
    Route::get('/audience/{audience}/modifier', ModifierAudience::class)->name('audience.modifier');
});

require __DIR__.'/settings.php';
