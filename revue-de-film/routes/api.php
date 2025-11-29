<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CriticController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\UserController;


// Route 1: Tous les films
Route::get('/films', [FilmController::class, 'index']);

// Route 9: Recherche de films
Route::get('/films/search', [FilmController::class, 'search']);

// Route 2: Acteurs d'un film
Route::get('/films/{film}/actors', [FilmController::class, 'actors']);

// Route 3: Film avec critiques
Route::get('/films/{film}', [FilmController::class, 'show']);

// Route 4: Créer un utilisateur
Route::post('/users', [UserController::class, 'store']);

// Route 5: Mettre à jour un utilisateur
Route::put('/users/{user}', [UserController::class, 'update']);

// Route 6: Supprimer une critique
Route::delete('/critics/{critic}', [CriticController::class, 'destroy']);

// Route 7: Moyenne des scores d'un film
Route::get('/films/{film}/average-score', [FilmController::class, 'averageScore']);

// Route 8: Langage préféré d'un utilisateur
Route::get('/users/{user}/preferred-language', [UserController::class, 'preferredLanguage']);

