<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard routes
Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
Route::get('/user/dashboard', [DashboardController::class, 'userDashboard'])->name('user.dashboard');

// Map routes
Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::post('/disturbances', [MapController::class, 'store'])->name('disturbances.store');
Route::put('/disturbances/{id}', [MapController::class, 'update'])->name('disturbances.update');
Route::delete('/disturbances/{id}', [MapController::class, 'destroy'])->name('disturbances.destroy');
Route::get('/disturbances', [MapController::class, 'getDisturbances'])->name('disturbances.get');
