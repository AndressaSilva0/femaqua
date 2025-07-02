<?php

<<<<<<< HEAD
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Cadastrar usuário
Route::post('/register', [UserController::class, 'store']);

// Listar todos os usuários (só para teste ou admin)
Route::get('/users', [UserController::class, 'index']);

// Exibir um usuário específico
Route::get('/users/{id}', [UserController::class, 'show']);

// Atualizar usuário
Route::put('/users/{id}', [UserController::class, 'update']);

// Deletar usuário
Route::delete('/users/{id}', [UserController::class, 'destroy']);
=======
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/test', function () {
    return response('teste');
});
>>>>>>> origin/main
