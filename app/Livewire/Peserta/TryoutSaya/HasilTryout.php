<?php

namespace App\Livewire\Peserta\TryoutSaya;

use App\Models\TryoutBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Layout('layouts.app')]
class HasilTryout extends Component
{
    public TryoutBooking $booking;

    public array $detail = [];

    public function mount(int $bookingId): void
    {
        abort_unless(Auth::check(), 401);

        $booking = TryoutBooking::query()
            ->with('tryoutPaket')
            ->where('user_id', Auth::id())
            ->find($bookingId);

        if (! $booking) {
            throw new NotFoundHttpException();
        }

        $this->booking = $booking;

        $this->detail = [
            'paket' => [
                'nama' => $booking->tryoutPaket?->nama,
                'deskripsi' => $booking->tryoutPaket?->deskripsi,
            ],
            'skor_total' => $booking->metadata['skor_total'] ?? null,
            'peringkat' => $booking->metadata['peringkat'] ?? null,
            'jawaban_benar' => $booking->metadata['jawaban_benar'] ?? null,
            'jawaban_salah' => $booking->metadata['jawaban_salah'] ?? null,
            'pembahasan' => $booking->metadata['pembahasan'] ?? [],
        ];
    }

    public function render()
    {
        return view('livewire.peserta.tryoutsaya.hasil')->layoutData([
            'title' => __('Hasil Tryout :nama', ['nama' => $this->booking->tryoutPaket->nama ?? __('Tidak diketahui')]),
        ]);
    }
}
