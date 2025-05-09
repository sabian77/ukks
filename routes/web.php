<?php

use Illuminate\Support\Facades\Route;
use App\Models\pkl;
use App\Models\siswa;
use App\Models\guru;
use App\Models\industri;
use Livewire\Volt\Volt;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('/pkl',  'pkl', ['pkl' => pkl::all()])->name('pkl');
Route::view('/siswa',  'siswa', ['siswa' => siswa::all()])->name('siswa');
Route::view('/guru',  'guru', ['guru' => guru::all()])->name('guru');
Route::view('/industri',  'industri', ['industri' => industri::all()])->name('industri');


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::get('/users', [AuthController::class, 'index']);

require __DIR__.'/auth.php';
