<?php

use App\Models\Role;
use App\Models\TryoutBooking;
use App\Models\TryoutPaket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createPesertaUser(): User
{
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $role = Role::firstOrCreate(['nama' => 'peserta'], [
        'deskripsi' => 'Peserta tryout',
    ]);

    $user->roles()->syncWithoutDetaching([$role->id]);

    return $user;
}

function createTryoutPaket(): TryoutPaket
{
    return TryoutPaket::create([
        'nama' => 'Tryout SKD Nasional',
        'waktu_pengerjaan' => 90,
        'harga' => 150000,
        'is_aktif' => 'aktif',
    ]);
}

test('guest users are redirected to login page when accessing tryout saya', function () {
    $this->get(route('peserta.tryout-saya'))
        ->assertRedirect('/login');
});

test('non-peserta users receive forbidden response', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('peserta.tryout-saya'))
        ->assertForbidden();
});

test('peserta can view their registered tryout bookings', function () {
    $user = createPesertaUser();

    $paket = createTryoutPaket();

    TryoutBooking::create([
        'user_id' => $user->id,
        'tryout_paket_id' => $paket->id,
        'status' => TryoutBooking::STATUS_PENDING,
        'tanggal_mulai' => now()->subMinutes(30),
        'tanggal_selesai' => now()->addMinutes(30),
        'durasi_menit' => 60,
        'harga' => 150000,
        'kode_pembayaran' => 'INV-TEST-1234',
    ]);

    $this->actingAs($user)
        ->get(route('peserta.tryout-saya'))
        ->assertOk()
        ->assertSeeText('Tryout Saya')
        ->assertSeeText('Tryout SKD Nasional')
        ->assertSeeText('Sedang berlangsung')
        ->assertSeeText('Lanjutkan Tryout');
});
