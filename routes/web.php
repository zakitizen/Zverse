<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PewartaController;
use App\Http\Controllers\RedaksiController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ─────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/article/{id}', [ArticleController::class, 'show'])->name('article.show');
Route::post('/article/{id}/like', [ArticleController::class, 'like'])->name('article.like');
Route::post('/article/{id}/comment', [ArticleController::class, 'comment'])->name('article.comment');
Route::get('/category/{category}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');
//Route::get('/shorts', [ShortsController::class, 'index'])->name('shorts');

// ─── Auth Universal ─────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect lama
Route::get('/pewarta/login', fn() => redirect()->route('login'))->name('pewarta.login');
Route::get('/redaksi/login', fn() => redirect()->route('login'))->name('redaksi.login');

// ─── Pewarta (Reporter) ─────────────────────────────────────────────────────────
Route::prefix('pewarta')->name('pewarta.')->middleware('pewarta')->group(function () {
    Route::post('/logout', [PewartaController::class, 'logout'])->name('logout');
    Route::get('/', [PewartaController::class, 'dashboard'])->name('dashboard');
    Route::get('/articles/create', [PewartaController::class, 'create'])->name('articles.create');
    Route::post('/articles', [PewartaController::class, 'store'])->name('articles.store');
    Route::post('/articles/upload-image', [PewartaController::class, 'uploadImage'])->name('articles.upload-image');
    Route::get('/articles/{id}/edit', [PewartaController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{id}', [PewartaController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{id}', [PewartaController::class, 'destroy'])->name('articles.destroy');
    Route::post('/articles/{id}/submit', [PewartaController::class, 'submit'])->name('articles.submit');
});

// ─── Redaksi (Editor) ──────────────────────────────────────────────────────────
Route::prefix('redaksi')->name('redaksi.')->middleware('redaksi')->group(function () {
    Route::post('/logout', [RedaksiController::class, 'logout'])->name('logout');
    Route::get('/', [RedaksiController::class, 'dashboard'])->name('dashboard');
    Route::post('/articles/upload-image', [PewartaController::class, 'uploadImage'])->name('articles.upload-image');
    Route::get('/articles/{id}/edit', [RedaksiController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{id}', [RedaksiController::class, 'update'])->name('articles.update');
    Route::post('/articles/{id}/approve', [RedaksiController::class, 'approve'])->name('articles.approve');
    Route::post('/articles/{id}/reject', [RedaksiController::class, 'reject'])->name('articles.reject');
    Route::post('/articles/{id}/publish', [RedaksiController::class, 'publish'])->name('articles.publish');
    Route::post('/articles/{id}/unpublish', [RedaksiController::class, 'unpublish'])->name('articles.unpublish');
    Route::delete('/articles/{id}', [RedaksiController::class, 'destroy'])->name('articles.destroy');
});
