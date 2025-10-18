<?php

namespace App\Livewire\Peserta\TryoutTersedia;

use App\Models\TryoutBooking;
use App\Models\TryoutPaket;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Layout('layouts.app')]
class DetailTryout extends Component
{
    public TryoutPaket $paket;

    public ?TryoutBooking $booking = null;

    public function mount(int $paketId): void
    {
        abort_unless(Auth::check(), 401);

        $user = Auth::user();

        abort_unless(
            $user?->roles()->where('nama', 'peserta')->exists(),
            403
        );

        $this->paket = TryoutPaket::query()
            ->where('is_aktif', 'aktif')
            ->find($paketId)
            ?? throw new NotFoundHttpException();

        $this->booking = $user?->tryoutBookings()
            ->where('tryout_paket_id', $this->paket->id)
            ->first();
    }

    public function render()
    {
        return view('livewire.peserta.tryouttersedia.detail')->layoutData([
            'title' => __('Detail Tryout :nama', ['nama' => $this->paket->nama]),
        ]);
    }
}
