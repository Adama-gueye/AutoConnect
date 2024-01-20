<?php

use App\Commentaire;
use App\Http\Controllers\AnnonceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlocController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\SignalementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [CompteController::class,'register']);

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class,'login']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('me', [AuthController::class,'me']);

});

Route::get('/annonceValides', [AnnonceController::class, 'annonceValides']);
Route::get('/annonces{id}', [AnnonceController::class, 'annoncesParCategorie']);
Route::post('/newsLetterStore', [NewsLetterController::class, 'store']);
Route::get('/blocs', [BlocController::class, 'index']);
Route::get('/annonceDetail{id}', [AnnonceController::class, 'detail']);
Route::post('/whatsap/{id}', [AnnonceController::class, 'redirigerWhatsApp'])->name("whatsapp.user");


Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/annonceInvalides', [AnnonceController::class, 'annonceInvalides']);
    Route::get('/annonceValidesByAdmin', [AnnonceController::class, 'annonceValides']);
    Route::patch('/index{id}', [AnnonceController::class, 'index']);
    Route::post('/categorieStore', [CategorieController::class, 'store']);
    Route::get('/categorieShow{id}', [CategorieController::class, 'show']);
    Route::patch('/categorieUpdate{id}', [CategorieController::class, 'update']);
    Route::delete('/categorieDestroy{id}', [CategorieController::class, 'destroy']);
    Route::get('/categories', [CategorieController::class, 'index']);
    Route::post('/blocStore', [BlocController::class, 'store']);
    Route::get('/blocShow{id}', [BlocController::class, 'show']);
    Route::patch('/blocUpdate{id}', [BlocController::class, 'update']);
    Route::delete('/blocDestroy{id}', [BlocController::class, 'destroy']);
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/commentaires', [CommentaireController::class, 'index']);
    Route::get('/newsLetters', [NewsLetterController::class, 'index']);
    Route::delete('/messageDestroy{id}', [MessageController::class, 'destroy']);
    Route::delete('/commentaireDestroy{id}', [CommentaireController::class, 'destroy']);
    Route::delete('/newsLetterDestroy{id}', [NewsLetterController::class, 'destroy']);
    Route::delete('/signalementDestroy{id}', [SignalementController::class, 'destroy']);
    Route::get('/signalements', [SignalementController::class, 'index']);
    Route::get('/listeProprietaire', [CompteController::class, 'listeProprietaire']);
    Route::get('/listeAcheteur', [CompteController::class, 'listeAcheteur']);
    //desactiver un utilisateur, activer un utiliser
});
Route::middleware(['auth:api', 'role:acheteur'])->group(function () {
    Route::post('/commentaireStore{id}', [CommentaireController::class, 'store']);
    Route::post('/messageStoreAcheteur', [MessageController::class, 'store']);
    Route::post('/signalementStore{id}', [SignalementController::class, 'store']);
});
Route::middleware(['auth:api', 'role:proprietaire'])->group(function () {
    Route::post('/annonceStore', [AnnonceController::class, 'store']);
    Route::get('/annonceShow{id}', [AnnonceController::class, 'show']);
    Route::patch('/annonceUpdate{id}', [AnnonceController::class, 'update']);
    Route::delete('/annonceDestroy{id}', [AnnonceController::class, 'destroy']);
    Route::delete('/commentaireDestroyProp{id}', [CommentaireController::class, 'destroy']);
    Route::get('/annonceUserValides', [AnnonceController::class, 'annonceUserValide']);
    Route::get('/annonceUserInvalides', [AnnonceController::class, 'annonceUserInvalide']);
    Route::post('/messageStore', [MessageController::class, 'store']);
    Route::get('/signalementProprietaire', [SignalementController::class, 'signalementProp']);
});
