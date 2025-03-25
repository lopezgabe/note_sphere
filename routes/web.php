<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard-mc', function () {
    return view('dashboard-mc');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('users', [UserController::class, 'index']);
Route::get('users-export', [UserController::class, 'export'])->name('users.export');
Route::post('users-import', [UserController::class, 'import'])->name('users.import');

Route::get('/notes', [NoteController::class, 'index']);
Route::get('/notes/import', [NoteController::class, 'notesImport']);
//Route::get('/notes/create', [NoteController::class, 'create']);
//Route::post('/notes', [NoteController::class, 'store'])->middleware('auth');
Route::get('/notes/{note}', [NoteController::class, 'show']);
Route::get('/notes/run-simulation/{id}', [SimulationController::class, 'runSimulation']);


Route::get('notes-export', [NoteController::class, 'export'])->name('notes.export');
Route::post('notes-import', [NoteController::class, 'import'])->name('notes.import');
