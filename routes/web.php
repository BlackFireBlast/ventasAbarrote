<?php

use App\Http\Controllers\categoriaController;
use App\Http\Controllers\clienteController;
use App\Http\Controllers\compraController;
use App\Http\Controllers\homeController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\logoutController;
use App\Http\Controllers\productoController;
use App\Http\Controllers\ventaController;
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


// Route::get('/', function () {
//     return view('template');
// });

// Route::view('/panel', 'panel.index')->name('panel');

Route::get('/',[homeController::class, 'index'])->name('panel');



//Route::view('/categorias', 'categoria.index')->name('categorias');
Route::resources([
'categorias'=> categoriaController::class,
'productos' => productoController::class,
'clientes' => clienteController::class,
'compras' => compraController::class,
'ventas' => ventaController::class,
]);



// Route::get('/login', function () {
//     return view('auth.login');
// });
Route::get('/login',[loginController::class,'index'])->name('login');
Route::post('/login',[loginController::class,'login']);//Maneja la lógica para iniciar sesión
Route::get('/logout',[logoutController::class, 'logout'])->name('logout');



Route::get('/401', function () {
    return view('pages.401');
});
Route::get('/404', function () {
    return view('pages.404');
});
Route::get('/500', function () {
    return view('pages.500');
});