<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\TagController;

// Rotas públicas
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [UserController::class, 'store'])->name('users.register');

// Rotas protegidas (JWT)
Route::middleware(['auth:api'])->group(function () {
    // Auth
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('me', [AuthController::class, 'me'])->name('auth.me');

    // User
    Route::get('users/list', [UserController::class, 'index'])->name('users.list');         // Lista todos os usuários
    Route::get('users/show/{id}', [UserController::class, 'show'])->name('users.show');     // Mostra usuário específico
    Route::put('users/update/{id}', [UserController::class, 'update'])->name('users.update'); // Atualiza usuário
    Route::delete('users/delete/{id}', [UserController::class, 'destroy'])->name('users.delete'); // Deleta usuário

    // Tool
    Route::get('tools', [ToolController::class, 'index'])->name('tools.list');
    Route::post('tools', [ToolController::class, 'store'])->name('tools.create');
    Route::delete('tools/{id}', [ToolController::class, 'destroy'])->name('tools.delete');

    // Tag
    Route::get('tags', [TagController::class, 'index'])->name('tags.list');
    Route::post('tags', [TagController::class, 'store'])->name('tags.create');
});
