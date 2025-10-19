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
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (TryoutBooking $booking) => $this->transformBooking($booking))
            ->toArray();

        $this->bookings = $bookings;

        $this->applyFilters();
    }

    protected function transformBooking(TryoutBooking $booking): array
    {
        return [
            'id' => $booking->id,
            'status' => $booking->status,
            'status_label' => match ($booking->status) {
                TryoutBooking::STATUS_PENDING => __('Menunggu konfirmasi'),
                TryoutBooking::STATUS_ACTIVE => __('Aktif'),
                TryoutBooking::STATUS_COMPLETED => __('Selesai'),
                TryoutBooking::STATUS_EXPIRED => __('Kedaluwarsa'),
                default => __('Tidak diketahui'),
            },
            'tanggal_mulai' => optional($booking->tanggal_mulai)->translatedFormat('d F Y'),
            'tanggal_selesai' => optional($booking->tanggal_selesai)->translatedFormat('d F Y'),
            'durasi_menit' => $booking->durasi_menit ?? $booking->tryoutPaket?->waktu_pengerjaan,
            'harga' => $booking->harga,
            'terdaftar' => optional($booking->created_at)->translatedFormat('d F Y'),
            'has_progress' => (bool) ($booking->metadata['progress'] ?? false),
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
            ->when($this->filterStatus !== 'all', fn($collection) => $collection->where('status', $this->filterStatus))
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
