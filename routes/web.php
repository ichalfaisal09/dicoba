<?php

use App\Livewire\Admin\Kategorisasi\Materi\Create as MateriCreate;
use App\Livewire\Admin\Kategorisasi\Materi\Index as MateriIndex;
use App\Livewire\Admin\Kategorisasi\Variasi\Create as VariasiCreate;
use App\Livewire\Admin\Kategorisasi\Variasi\Index as VariasiIndex;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Admin\Kategorisasi\Subtes\Create as SubtesCreate;
use App\Livewire\Admin\Kategorisasi\Subtes\Index as SubtesIndex;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = request()->user();

        $isAdmin = $user->roles()->where('nama', 'admin')->exists();

        if ($isAdmin) {
            return view('livewire.admin.dashboard');
        }

        return view('livewire.peserta.dashboard');
    })->name('dashboard');

    Route::get('admin/kategorisasi/subtes', SubtesIndex::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.kategorisasi.subtes');

    Route::get('admin/kategorisasi/subtes/create', SubtesCreate::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.kategorisasi.subtes.create');

    Route::get('admin/kategorisasi/materi', MateriIndex::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.kategorisasi.materi');

    Route::get('admin/kategorisasi/materi/create', MateriCreate::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.kategorisasi.materi.create');

    Route::get('admin/kategorisasi/variasi', VariasiIndex::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.kategorisasi.variasi');

    Route::get('admin/kategorisasi/variasi/create', VariasiCreate::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.kategorisasi.variasi.create');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

require __DIR__.'/auth.php';
