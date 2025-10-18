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

    public array $filteredBookings = [];

    public string $filterStatus = 'all';

    public string $search = '';

    public function mount(): void
    {
        abort_unless(Auth::check(), 401);

        abort_unless(
            Auth::user()?->roles()->where('nama', 'peserta')->exists(),
            403
        );

        $this->loadBookings();
    }

    public function updatedFilterStatus(): void
    {
        $this->applyFilters();
    }

    public function updatedSearch(): void
    {
        $this->applyFilters();
    }

    public function refreshBookings(): void
    {
        $this->loadBookings();
    }

    public function render()
    {
        return view('livewire.peserta.tryoutsaya.list')->layoutData([
            'title' => __('Tryout Saya'),
        ]);
    }

    protected function loadBookings(): void
    {
        $bookings = TryoutBooking::query()
            ->with('tryoutPaket')
            ->where('user_id', Auth::id())
            ->orderByDesc('tanggal_mulai')
            ->get()
            ->map(function (TryoutBooking $booking) {
                $updatedStatus = $this->determineStatus($booking);

                if ($updatedStatus !== $booking->status) {
                    $booking->update(['status' => $updatedStatus]);
                    $booking->refresh();
                }

                return $this->transformBooking($booking);
            })
            ->toArray();

        $this->bookings = $bookings;

        $this->applyFilters();
    }

    protected function determineStatus(TryoutBooking $booking): string
    {
        $status = $booking->status;

        if ($booking->tanggal_mulai && $status === TryoutBooking::STATUS_PENDING && $booking->tanggal_mulai->isPast()) {
            $status = TryoutBooking::STATUS_ACTIVE;
        }

        if (
            $booking->tanggal_selesai
            && in_array($status, [TryoutBooking::STATUS_ACTIVE, TryoutBooking::STATUS_PENDING], true)
            && $booking->tanggal_selesai->isPast()
        ) {
            $status = TryoutBooking::STATUS_COMPLETED;
        }

        return $status;
    }

    protected function transformBooking(TryoutBooking $booking): array
    {
        return [
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
        ];
    }

    protected function applyFilters(): void
    {
        $filtered = collect($this->bookings)
            ->when($this->filterStatus !== 'all', fn ($collection) => $collection->where('status', $this->filterStatus))
            ->when($this->search !== '', function ($collection) {
                $search = mb_strtolower($this->search);

                return $collection->filter(function ($booking) use ($search) {
                    return str_contains(mb_strtolower($booking['paket']['nama'] ?? ''), $search);
                });
            })
            ->values()
            ->toArray();

        $this->filteredBookings = $filtered;
    }
}
