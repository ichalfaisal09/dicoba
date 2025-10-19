<?php

namespace App\Livewire\Peserta\TryoutUjian;

use App\Models\TryoutBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Layout('layouts.app')]
class Index extends Component
{
    public TryoutBooking $booking;

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
    }

    public function render()
    {
        return view('livewire.peserta.tryoutujian.index')->layoutData([
            'title' => __('Tata Tertib Ujian Tryout'),
        ]);
    }
}
