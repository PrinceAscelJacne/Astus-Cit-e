<?php

use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\TablesController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
| Seul le site vitrine est accessible sans authentification. Les deux points
| d'entrée en écriture sont limités en débit : ils écrivent en base et
| déclenchent un envoi de mail sans qu'aucun compte ne soit requis.
*/

Route::get('/', function () {
    return view('frontend');
});

Route::post('/frontend/sendmail', [FrontendController::class, 'sendmail'])
    ->middleware('throttle:5,1')
    ->name('sendmail');

Route::post('/frontend/storerdv', [FrontendController::class, 'storerdv'])
    ->middleware('throttle:5,1')
    ->name('storerdv');

/*
|--------------------------------------------------------------------------
| Routes authentifiées
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Navigation
    Route::get('/redirect', [DashboardController::class, 'redirect'])->name('redirect');
    Route::get('/dashboarde/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboarde/archives', [DashboardController::class, 'archives'])->name('archives');
    Route::get('/dashboarde/nouveautes', [DashboardController::class, 'nouveautes'])->name('nouveautes');
    Route::get('/dashboarde/aide', [DashboardController::class, 'aide'])->name('aide');
    Route::get('/dashboarde/apropos', [DashboardController::class, 'apropos'])->name('apropos');

    // Projets — la lecture est filtrée par département dans le contrôleur,
    // les écritures sont contrôlées par ProjectPolicy.
    Route::get('/dashboarde/viewproject', [ProjetController::class, 'index'])->name('viewproject');
    Route::get('/dashboarde/projetarchives', [ProjetController::class, 'projetarchives'])->name('projetarchives');
    Route::get('/dashboarde/modifyproject/{id}', [ProjetController::class, 'modify'])->name('modifyproject');
    Route::match(['put', 'patch', 'post'], '/dashboarde/updateproject/{id}', [ProjetController::class, 'update'])->name('updateproject');
    Route::delete('/dashboarde/deleteproject/{id}', [ProjetController::class, 'delete'])->name('deleteproject');
    Route::match(['put', 'patch', 'post'], '/dashboarde/archiverproject/{id}', [ProjetController::class, 'archiverproject'])->name('archiverproject');

    // Création de projet : chefs de département et Boss uniquement.
    Route::middleware('role:' . Role::CHEF_DEPARTEMENT . ',' . Role::BOSS)->group(function () {
        Route::get('/dashboarde/createproject', [ProjetController::class, 'create'])->name('createproject');
        Route::post('/dashboarde/storeproject', [ProjetController::class, 'store'])->name('storeproject');
    });

    // Fichiers — chaque accès unitaire est contrôlé par FilePolicy.
    Route::get('/dashboarde/viewfile', [FileController::class, 'index'])->name('viewfile');
    Route::get('/dashboarde/createfile', [FileController::class, 'create'])->name('createfile');
    Route::post('/dashboarde/storefile', [FileController::class, 'store'])->name('storefile');
    Route::get('/dashboarde/modifyfile/{id}', [FileController::class, 'modify'])->name('modifyfile');
    Route::put('/dashboarde/updatefile', [FileController::class, 'update'])->name('updatefile');
    Route::delete('/dashboarde/deletefile/{id}', [FileController::class, 'delete'])->name('deletefile');
    Route::get('/dashboarde/downloadfile/{id}', [FileController::class, 'download'])->name('downloadfile');
    Route::get('/dashboarde/archivesfiles', [FileController::class, 'archives'])->name('archivesfiles');
    Route::get('/get-file-content/{filename}', [FileController::class, 'getFileContent'])->name('getfilecontent');

    // Brouillons
    Route::get('/dashboarde/brouillonfiles', [FileController::class, 'brouillon'])->name('brouillonfiles');
    Route::delete('/dashboarde/deletebrouillonfiles', [FileController::class, 'deletebrouillon'])->name('deletebrouillonfiles');

    // Compte de l'utilisateur courant
    Route::get('/auth/profileshower', [Authcontroller::class, 'edit'])->name('profileshower');
    Route::post('/auth/profileupdateinfos', [Authcontroller::class, 'updateinfos'])->name('updateinfos');
    Route::post('/auth/profileupdatepassword', [Authcontroller::class, 'updatepassword'])->name('updatepassword');
    Route::get('/auth/profile/sessions', [Authcontroller::class, 'index'])->name('profilesessions');
    Route::post('/auth/profile/sessions/logout-others', [Authcontroller::class, 'logoutOtherSessions'])->name('profile.sessions.logoutOthers');
    Route::post('/auth/profile/delete-account', [Authcontroller::class, 'deleteAccount'])->name('delete-account');

    // Inscription d'utilisateurs : chefs de département et Boss.
    // Le rôle et le département réellement attribuables sont revérifiés
    // dans Authcontroller::store.
    Route::middleware('role:' . Role::CHEF_DEPARTEMENT . ',' . Role::BOSS)->group(function () {
        Route::get('/auth/registerauth', [Authcontroller::class, 'register'])->name('registerauth');
        Route::post('/auth/storeauth', [Authcontroller::class, 'store'])->name('storeauth');
    });

    // Administration — réservée au Boss.
    Route::middleware('role:' . Role::BOSS)->group(function () {
        Route::get('/dashboarde/utilisateurs', [TablesController::class, 'utilisateurs'])->name('utilisateurs');
        Route::get('/dashboarde/roles', [TablesController::class, 'roles'])->name('roles');
        Route::get('/dashboarde/departements', [TablesController::class, 'departements'])->name('departements');
        Route::get('/dashboarde/tablefile', [TablesController::class, 'files'])->name('files');
        Route::get('/dashboarde/tableproject', [TablesController::class, 'projects'])->name('projects');

        Route::post('/dashboarde/createdep', [TablesController::class, 'createdep'])->name('createdep');

        // NB : les routes modifyuser / modifyrole / modifydep ont été retirées.
        // Elles pointaient vers des méthodes qui n'ont jamais été écrites
        // (500 systématique). L'édition d'un utilisateur reste à implémenter.

        Route::delete('/dashboarde/delete/{table}/{id}', [TablesController::class, 'delete'])->name('delete');
        Route::post('/dashboarde/archiverfile/{id}', [TablesController::class, 'archiverfile'])->name('archiverfile');

        // Messages de contact et demandes de rendez-vous du site vitrine :
        // données personnelles de tiers, donc accès restreint.
        Route::get('/frontend/admin/dashboard', [FrontendController::class, 'dashboard'])->name('frontend-dashboard');
    });
});
