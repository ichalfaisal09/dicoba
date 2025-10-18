<?php

namespace App\Livewire\Peserta\TryoutSaya;

use App\Models\TryoutBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ListTryoutSaya extends Component
{
    public array $bookings = [];

    public function mount(): void
    {
        abort_unless(Auth::check(), 401);

        abort_unless(
            Auth::user()?->roles()->where('nama', 'peserta')->exists(),
            403
        );

        $this->bookings = TryoutBooking::query()
            ->with('tryoutPaket')
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(fn (TryoutBooking $booking) => [
                'id' => $booking->id,
                'status' => $booking->status,
                'status_label' => match ($booking->status) {
                    TryoutBooking::STATUS_PENDING => __('Menunggu konfirmasi'),
                    TryoutBooking::STATUS_ACTIVE => __('Sedang berlangsung'),
                    TryoutBooking::STATUS_COMPLETED => __('Selesai'),
                    TryoutBooking::STATUS_EXPIRED => __('Kedaluwarsa'),
                    default => __('Tidak diketahui'),
                },
                'tanggal_mulai' => optional($booking->tanggal_mulai)->translatedFormat('d M Y, H:i'),
                'tanggal_selesai' => optional($booking->tanggal_selesai)->translatedFormat('d M Y, H:i'),
                'durasi_menit' => $booking->durasi_menit,
                'harga' => $booking->harga,
                'paket' => [
                    'id' => $booking->tryoutPaket?->id,
                    'nama' => $booking->tryoutPaket?->nama,
                    'deskripsi' => $booking->tryoutPaket?->deskripsi,
                ],
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.peserta.tryoutsaya.list')->layoutData([
            'title' => __('Tryout Saya'),
        ]);
    }
}
