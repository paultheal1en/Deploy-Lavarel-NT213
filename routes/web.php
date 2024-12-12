<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrokenAccessControlController;

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
Route::get('/store/{filename}', function ($filename) {
    $filePath = public_path('store/' . $filename);
    
    if (!file_exists($filePath)) {
        abort(404, 'File không tồn tại'); 
    }
    
    return response()->file($filePath);
});
Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['csp'])->group(function () {
    Route::get('/xss', [XSSController::class, 'index']);
});
Route::get('/lfi','App\Http\Controllers\LFIController@index');
Route::post('/lfi','App\Http\Controllers\LFIController@lfi');
Route::match(['get','post'],'/xss','App\Http\Controllers\XSSController@index');
Route::match(['get','post'],'/xxe','App\Http\Controllers\XXEController@index');
Route::get('/upload','App\Http\Controllers\FileUploadController@index');   
Route::post('/upload','App\Http\Controllers\FileUploadController@upload');
Route::get('/store/{filename?}','App\Http\Controllers\FileUploadController@getFile');
Route::get('/sqli/{id}','App\Http\Controllers\SQLiController@getUser');
Route::get('/ssrf','App\Http\Controllers\SSRFController@index');
Route::get('/cachepoisoning','App\Http\Controllers\CachePoisoningController@index');
Route::get('/brac','App\Http\Controllers\BrokenAccessControlController@showNote');
Route::post('/brac','App\Http\Controllers\BrokenAccessControlController@insertNote');
Route::get('/brac/{PostID?}','App\Http\Controllers\BrokenAccessControlController@showSpecificNote');
Route::get('/csrf','App\Http\Controllers\CSRFController@index');
Route::post('/csrf','App\Http\Controllers\CSRFController@changeEmail');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:3,1');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:2,1')->name('register');
Route::get('/logout', [AuthController::class, 'logout']);
Route::get('/brac', function () {
    if (!Session::has('user_id')) {
        return redirect('/login');
    }
    $controller = app(BrokenAccessControlController::class);
    return $controller->showNote(request());
});