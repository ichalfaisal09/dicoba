<?php

use App\Livewire\Admin\Kategorisasi\Materi\Create as MateriCreate;
use App\Livewire\Admin\Kategorisasi\Materi\Index as MateriIndex;
use App\Livewire\Admin\Kategorisasi\Variasi\Create as VariasiCreate;
use App\Livewire\Admin\Kategorisasi\Variasi\Index as VariasiIndex;
use App\Livewire\Admin\Konfigurasi\Create as KonfigurasiCreate;
use App\Livewire\Admin\Konfigurasi\Index as KonfigurasiIndex;
use App\Livewire\Admin\ManajemenSoal\Tiu\CreateTiu;
use App\Livewire\Admin\ManajemenSoal\Tiu\ImportTiu;
use App\Livewire\Admin\ManajemenSoal\Tiu\ListTiu;
use App\Livewire\Admin\ManajemenSoal\Tkp\CreateTkp;
use App\Livewire\Admin\ManajemenSoal\Tkp\ImportTkp;
use App\Livewire\Admin\ManajemenSoal\Tkp\ListTkp;
use App\Livewire\Admin\ManajemenSoal\Twk\ImportTwk;
use App\Livewire\Admin\ManajemenSoal\Twk\CreateTwk;
use App\Livewire\Admin\ManajemenSoal\Twk\ListTwk;
use App\Livewire\GeneratorJson;
use App\Livewire\Peserta\TryoutTersedia\ListTryoutTersedia;
use App\Livewire\Peserta\TryoutTersedia\DetailTryout;
use App\Livewire\Peserta\TryoutSaya\ListTryoutSaya;
use App\Livewire\Admin\ManajemenTryout\Paket\Create as PaketCreate;
use App\Livewire\Admin\ManajemenTryout\Paket\Index as PaketIndex;
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

    Route::get('admin/konfigurasi', KonfigurasiIndex::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.konfigurasi');

    Route::get('admin/konfigurasi/create', KonfigurasiCreate::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.konfigurasi.create');

    Route::get('admin/manajemen-tryout/paket', PaketIndex::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.manajemen-tryout.paket');

    Route::get('admin/manajemen-tryout/paket/create', PaketCreate::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.manajemen-tryout.paket.create');

    Route::get('admin/manajemen-soal/twk', ListTwk::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.manajemen-soal.twk');

    Route::get('admin/manajemen-soal/twk/create', CreateTwk::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.manajemen-soal.twk.create');

    Route::get('admin/manajemen-soal/twk/import', ImportTwk::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.manajemen-soal.twk.import');

    Route::get('admin/manajemen-soal/tiu', ListTiu::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.manajemen-soal.tiu');

    Route::get('admin/manajemen-soal/tiu/create', CreateTiu::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.manajemen-soal.tiu.create');

    Route::get('admin/manajemen-soal/tiu/import', ImportTiu::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.manajemen-soal.tiu.import');

    Route::get('admin/manajemen-soal/tkp', ListTkp::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.manajemen-soal.tkp');

    Route::get('admin/manajemen-soal/tkp/create', CreateTkp::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.manajemen-soal.tkp.create');

    Route::get('admin/manajemen-soal/tkp/import', ImportTkp::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.manajemen-soal.tkp.import');

    Route::get('admin/generator-json', GeneratorJson::class)
        ->middleware(['auth', 'verified'])
        ->name('admin.generator-json');
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

    Route::get('peserta/tryout-tersedia', ListTryoutTersedia::class)
        ->middleware(['verified'])
        ->name('peserta.tryout-tersedia');

    Route::get('peserta/tryout/{paketId}', DetailTryout::class)
        ->whereNumber('paketId')
        ->middleware(['verified'])
        ->name('peserta.tryout.detail');

    Route::get('peserta/tryout-saya', ListTryoutSaya::class)
        ->middleware(['verified'])
        ->name('peserta.tryout-saya');
});

require __DIR__.'/auth.php';
