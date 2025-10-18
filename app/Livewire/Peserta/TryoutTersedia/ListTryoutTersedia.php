<?php

namespace App\Livewire\Peserta\TryoutTersedia;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ListTryoutTersedia extends Component
{
    public array $paket = [];

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->roles()->where('nama', 'peserta')->exists(),
            403
        );

        $this->paket = [
            [
                'nama' => 'Tryout SKD Nasional 2025',
                'status' => __('Aktif'),
                'badge' => 'success',
                'mulai' => now()->addDays(2)->translatedFormat('d M Y, H:i'),
                'selesai' => now()->addDays(7)->translatedFormat('d M Y, H:i'),
                'deskripsi' => __('Simulasi SKD favorit dengan rangkuman soal terbaru dan ranking nasional real-time.'),
            ],
            [
                'nama' => 'Simulasi TIU Intensif',
                'status' => __('Segera Dibuka'),
                'badge' => 'warning',
                'mulai' => now()->addDays(5)->translatedFormat('d M Y, H:i'),
                'selesai' => now()->addDays(10)->translatedFormat('d M Y, H:i'),
                'deskripsi' => __('Fokus pada penalaran logika, numerik, dan verbal dengan pembahasan detail.'),
            ],
            [
                'nama' => 'Latihan TKP Terstruktur',
                'status' => __('Aktif'),
                'badge' => 'info',
                'mulai' => now()->subDays(1)->translatedFormat('d M Y, H:i'),
                'selesai' => now()->addDays(3)->translatedFormat('d M Y, H:i'),
                'deskripsi' => __('Uji karakteristik pribadi dengan analisis skor keterampilan interpersonal dan pelayanan publik.'),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.peserta.tryouttersedia.list')->layoutData([
            'title' => __('Tryout Tersedia'),
        ]);
    }
}
