<?php
use App\Http\Controllers\EventsController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ArrearController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\StreetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VillaController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('streets', StreetController::class)->only(['index']);
    Route::resource('villas', VillaController::class)->only(['index']);
    Route::resource('contributions', ContributionController::class)->only(['index']);
    Route::resource('expenses', ExpenseController::class)->only(['index']);
    Route::resource('announcements', AnnouncementController::class)->only(['index']);
    Route::get('announcements/{announcement}/pdf', [AnnouncementController::class, 'pdf'])->name('announcements.pdf');
    Route::get('bulletins', [BulletinController::class, 'index'])->name('bulletins.index');
    Route::get('arrears', [ArrearController::class, 'index'])->name('arrears.index');
    Route::resource('events', EventsController::class)->only(['index']);

    Route::middleware('role:admin,gestionnaire')->group(function () {
        Route::resource('events', EventsController::class)->except(['index', 'show']);
        Route::resource('streets', StreetController::class)->except(['index', 'show']);
        Route::resource('villas', VillaController::class)->except(['index', 'show']);
        Route::resource('contributions', ContributionController::class)->except(['index', 'show']);
        Route::resource('expenses', ExpenseController::class)->except(['index', 'show']);
        Route::resource('announcements', AnnouncementController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
        Route::get('announcements/{announcement}/send', [AnnouncementController::class, 'send'])->name('announcements.send');
        Route::post('announcements/{announcement}/dispatch', [AnnouncementController::class, 'dispatch'])->name('announcements.dispatch');
    });

    Route::resource('announcements', AnnouncementController::class)->only(['show']);

    Route::resource('users', UserController::class)->except(['show'])->middleware('admin');
    Route::get('exports/{resource}/{format}', ExportController::class)
        ->whereIn('resource', ['streets', 'villas', 'contributions', 'expenses', 'announcements'])
        ->whereIn('format', ['pdf', 'excel'])
        ->name('exports');
});
