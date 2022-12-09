<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AnimalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'api'], function() {
    Route::get('/animal_kinds', [AnimalController::class, 'getAnimalKinds']);
    Route::get('/animals', [AnimalController::class, 'getAnimals']);
    Route::delete('/animals/{id}', [AnimalController::class, 'deleteAnimal']);
    Route::patch('/animals/{id}', [AnimalController::class, 'toGetOld']);
    Route::get('/animals/{id}', [AnimalController::class, 'getAnimal']);
    Route::post('/animal/create', [AnimalController::class, 'createAnimal']);
});
