<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PewartaController;
use App\Http\Controllers\RedaksiController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ─────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/article/{id}', [ArticleController::class, 'show'])->name('article.show');
Route::get('/category/{category}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::middleware('auth')->group(function () {
    Route::post('/article/{article}/comments', [CommentController::class, 'store'])->name('article.comments.store');
    Route::post('/article/{article}/comments/{comment}/reply', [CommentController::class, 'reply'])->name('article.comments.reply');
    Route::patch('/article/{article}/comments/{comment}', [CommentController::class, 'update'])->name('article.comments.update');
    Route::delete('/article/{article}/comments/{comment}', [CommentController::class, 'destroy'])->name('article.comments.destroy');
    Route::get('/article/{article}/comments/{comment}/replies', [CommentController::class, 'loadReplies'])->name('article.comments.replies');
});

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
    Route::post('/articles/upload-image', [RedaksiController::class, 'uploadImage'])->name('articles.upload-image');
    Route::get('/articles/{id}/edit', [RedaksiController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{id}', [RedaksiController::class, 'update'])->name('articles.update');
    Route::post('/articles/{id}/approve', [RedaksiController::class, 'approve'])->name('articles.approve');
    Route::post('/articles/{id}/reject', [RedaksiController::class, 'reject'])->name('articles.reject');
    Route::post('/articles/{id}/publish', [RedaksiController::class, 'publish'])->name('articles.publish');
    Route::post('/articles/{id}/unpublish', [RedaksiController::class, 'unpublish'])->name('articles.unpublish');
    Route::delete('/articles/{id}', [RedaksiController::class, 'destroy'])->name('articles.destroy');
});
