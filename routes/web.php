<?php

use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\TablesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;





Route::get('/', function () {
    return view('frontend');
});
route::post('/frontend/sendmail', [FrontendController::class, 'sendmail'])->name('sendmail');
route::post('/frontend/storerdv', [FrontendController::class, 'storerdv'])->name('storerdv');
Route::get('/frontend/admin/dashboard', [FrontendController::class, 'dashboard'])->name('frontend-dashboard');


/*Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});*/



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // routes pour la navigation
    route::get('/redirect', [DashboardController::class, 'redirect'])->name('redirect');
    route::get('/dashboarde/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    route::get('/dashboarde/archives', [DashboardController::class, 'archives'])->name('archives');
    route::get('/dashboarde/nouveautes', [DashboardController::class, 'nouveautes'])->name('nouveautes');
    route::get('/dashboarde/aide', [DashboardController::class, 'aide'])->name('aide');
    route::get('/dashboarde/apropos', [DashboardController::class, 'apropos'])->name('apropos');

    // routes pour les projets

    route::get('/dashboarde/viewproject', [ProjetController::class, 'index'])->name('viewproject');
    route::get('/dashboarde/createproject', [ProjetController::class, 'create'])->name('createproject');
    route::post('/dashboarde/storeproject', [ProjetController::class, 'store'])->name('storeproject');
    route::get('/dashboarde/modifyproject/{id}', [ProjetController::class, 'modify'])->name('modifyproject');
    route::any('/dashboarde/updateproject/{id}', [ProjetController::class, 'update'])->name('updateproject');
    route::delete('/dashboarde/deleteproject/{id}', [ProjetController::class, 'delete'])->name('deleteproject');

    route::any('/dashboarde/archiverproject/{id}', [ProjetController::class, 'archiverproject'])->name('archiverproject');
    route::get('/dashboarde/projetarchives', [ProjetController::class, 'projetarchives'])->name('projetarchives');


    // routes pour les fichiers

    route::get('/dashboarde/viewfile', [FileController::class, 'index'])->name('viewfile');
    route::get('/dashboarde/createfile', [FileController::class, 'create'])->name('createfile');
    route::post('/dashboarde/storefile', [FileController::class, 'store'])->name('storefile');
    route::get('/dashboarde/modifyfile/{id}', [FileController::class, 'modify'])->name('modifyfile');
    route::put('/dashboarde/updatefile', [FileController::class, 'update'])->name('updatefile');
    route::delete('/dashboarde/deletefile/{id}', [FileController::class, 'delete'])->name('deletefile');
    Route::get('/get-file-content/{filePath}', [FIleController::class, 'getFileContent']);


    route::get('/dashboarde/downloadfile{id}', [FileController::class, 'download'])->name('downloadfile');
    route::get('/dashboarde/archivesfiles', [FileController::class, 'archives'])->name('archivesfiles');
                        /*Brouillon*/
    route::get('/dashboarde/brouillonfiles', [FileController::class, 'brouillon'])->name('brouillonfiles');
    route::get('/dashboarde/storebrouillonfiles', [FileController::class, 'storebrouillon'])->name('storebrouillonfiles');
    route::delete('/dashboarde/deletebrouillonfiles', [FileController::class, 'deletebrouillon'])->name('deletebrouillonfiles');



    // routes pour l'authentification

    route::get('/auth/registerauth', [Authcontroller::class, 'register'])->name('registerauth');
    route::post('/auth/storeauth', [Authcontroller::class, 'store'])->name('storeauth');
    route::get('/auth/profileshower', [Authcontroller::class, 'edit'])->name('profileshower');
    route::post('/auth/profileupdateinfos', [Authcontroller::class, 'updateinfos'])->name('updateinfos');
    route::post('/auth/profileupdatepassword', [Authcontroller::class, 'updatepassword'])->name('updatepassword');
    Route::get('/auth/profile/sessions', [AuthController::class, 'index'])->name('profilesessions');
    Route::post('/auth/profile/sessions/logout-others', [AuthController::class, 'logoutOtherSessions'])->name('profile.sessions.logoutOthers');
    Route::post('/auth/profile/delete-account', [AuthController::class, 'deleteAccount'])->name('delete-account');

    //routes pour tables controller

    route::get('/dashboarde/utilisateurs', [TablesController::class, 'utilisateurs'])->name('utilisateurs');
    route::get('/dashboarde/roles', [TablesController::class, 'roles'])->name('roles');
    route::get('/dashboarde/departements', [TablesController::class, 'departements'])->name('departements');
    route::get('/dashboarde/tablefile', [TablesController::class, 'files'])->name('files');
    route::get('/dashboarde/tableproject', [TablesController::class, 'projects'])->name('projects');

    /*Creations */
    route::post('/dashboarde/createdep', [TablesController::class, 'createdep'])->name('createdep');

    /*Modifications*/
    route::get('/dashboarde/modifyuser{id}', [TablesController::class, 'modifyuser'])->name('modifyuser');
    route::get('/dashboarde/modifyrole{id}', [TablesController::class, 'modifyrole'])->name('modifyrole');
    route::get('/dashboarde/modifydep{id}', [TablesController::class, 'modifydep'])->name('modifydep');


    /*suppression*/
    route::delete('/dashboarde/delete/{table}/{id}', [TablesController::class, 'delete'])->name('delete');
    /** archiver fichier */
    route::post('/dashboarde/archiverfile/{id}', [TablesController::class, 'archiverfile'])->name('archiverfile');

});




