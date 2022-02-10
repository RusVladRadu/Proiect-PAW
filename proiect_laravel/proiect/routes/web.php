<?php

use App\Http\Controllers\PagesController;
use App\Http\Controllers\PostsController;
use Illuminate\Support\Facades\Route;

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
Route::get('/',[PagesController::class, 'index']); // apelare metoda index() din constrollerul PagesController

Route::resource('/blog', PostsController::class); // utilizeaza metodele controllerului PostsController la accesarea resursei '/blog'
//Route::get('/blog',[PostsController::class, 'show']);
//Route::get('/blog/<slug>/edit',[PostsController::class, 'blog.edit']);

Auth::routes(); // toate rutele pentru autentificare (SwiftMailer)

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home'); // apeleaza metoda index() din controllerul HomeController si specifica numele 'home' pentru randarea principala

