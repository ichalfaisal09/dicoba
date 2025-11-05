<?php

namespace App\Livewire\Peserta\TryoutTersedia;

use App\Models\TryoutBooking;
use App\Models\TryoutPaket;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')]
class ListTryoutTersedia extends Component
{
    public array $paket = [];

    public array $statistik = [];

    public bool $konfirmasiModal = false;

    public array $paketKonfirmasi = [];

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->roles()->where('nama', 'peserta')->exists(),
            403
        );

        $this->loadPaket();
    }

    public function render()
    {
        return view('livewire.peserta.tryouttersedia.list')->layoutData([
            'title' => __('Tryout Tersedia'),
        ]);
    }

    public function confirmRegister(int $paketId): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        $paket = TryoutPaket::query()
            ->where('is_aktif', 'aktif')
            ->find($paketId);

        if (! $paket) {
            session()->flash('callout', [
                'icon' => 'exclamation-circle',
                'variant' => 'danger',
                'heading' => __('Paket tidak ditemukan'),
                'text' => __('Paket tryout yang kamu pilih tidak tersedia atau sudah nonaktif.'),
            ]);

            return;
        }

        $existing = $user->tryoutBookings()
            ->where('tryout_paket_id', $paketId)
            ->first();

        if ($existing && in_array($existing->status, [
            TryoutBooking::STATUS_PENDING,
            TryoutBooking::STATUS_ACTIVE,
        ], true)) {
            session()->flash('callout', [
                'icon' => 'information-circle',
                'variant' => 'warning',
                'heading' => __('Kamu sudah terdaftar'),
                'text' => __('Paket :nama sedang dalam status :status.', [
                    'nama' => $paket->nama,
                    'status' => __($existing->status),
                ]),
            ]);

            return;
        }

        $this->paketKonfirmasi = [
            'id' => $paket->id,
            'nama' => $paket->nama,
            'deskripsi' => $paket->deskripsi ?? __('Belum ada deskripsi paket.'),
            'harga' => $paket->harga,
            'waktu_pengerjaan' => $paket->waktu_pengerjaan,
            'status' => $existing?->status,
        ];

        $this->konfirmasiModal = true;
    }

    public function registerConfirmed(): void
    {
        if (! $this->paketKonfirmasi) {
            return;
        }

        $paketId = $this->paketKonfirmasi['id'];

        $this->konfirmasiModal = false;

        $this->register($paketId);

        $this->paketKonfirmasi = [];
    }

    public function cancelRegister(): void
    {
        $this->konfirmasiModal = false;
        $this->paketKonfirmasi = [];
    }

    public function register(int $paketId)
    {
        $user = Auth::user();

        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        $paket = TryoutPaket::query()
            ->where('is_aktif', 'aktif')
            ->find($paketId);

        if (! $paket) {
            session()->flash('callout', [
                'icon' => 'exclamation-circle',
                'variant' => 'danger',
                'heading' => __('Paket tidak ditemukan'),
                'text' => __('Paket tryout yang kamu pilih tidak tersedia atau sudah nonaktif.'),
            ]);

            return;
        }

        $existing = $user->tryoutBookings()
            ->where('tryout_paket_id', $paketId)
            ->first();

        if ($existing && in_array($existing->status, [
            TryoutBooking::STATUS_PENDING,
            TryoutBooking::STATUS_ACTIVE,
        ], true)) {
            session()->flash('callout', [
                'icon' => 'information-circle',
                'variant' => 'warning',
                'heading' => __('Kamu sudah terdaftar'),
                'text' => __('Paket :nama sedang dalam status :status.', [
                    'nama' => $paket->nama,
                    'status' => __($existing->status),
                ]),
            ]);

            $this->konfirmasiModal = false;

            return;
        }

        $payload = [
            'status' => TryoutBooking::STATUS_ACTIVE,
            // 'tanggal_mulai' => null,
            // 'tanggal_selesai' => null,
            // 'durasi_menit' => null,
            'harga' => $paket->harga,
            'kode_pembayaran' => sprintf('INV-%s-%s', now()->format('YmdHis'), str_pad((string) $user->id, 4, '0', STR_PAD_LEFT)),
            'metadata' => $existing?->metadata ?? [],
        ];

        try {
            if ($existing) {
                $existing->update($payload);
            } else {
                $user->tryoutBookings()->create(array_merge($payload, [
                    'tryout_paket_id' => $paket->id,
                ]));
            }

            session()->flash('callout', [
                'icon' => 'check-circle',
                'variant' => 'success',
                'heading' => __('Pendaftaran tryout berhasil'),
                'text' => __('Kami sudah mencatat pendaftaran kamu pada paket :nama.', ['nama' => $paket->nama]),
            ]);

            return $this->redirectRoute('peserta.tryout-saya');
        } catch (Throwable $throwable) {
            report($throwable);

            session()->flash('callout', [
                'icon' => 'exclamation-circle',
                'variant' => 'danger',
                'heading' => __('Terjadi kesalahan'),
                'text' => __('Silakan coba lagi beberapa saat lagi.'),
            ]);
        }

        $this->loadPaket();

        $this->konfirmasiModal = false;
        $this->paketKonfirmasi = [];
    }

    protected function loadPaket(): void
    {
        $user = Auth::user();

        $bookings = $user?->tryoutBookings()
            ->get(['id', 'tryout_paket_id', 'status'])
            ->keyBy('tryout_paket_id');

        $paketCollection = TryoutPaket::query()
            ->where('is_aktif', 'aktif')
            ->withCount('bookings')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (TryoutPaket $paket) use ($bookings) {
                $booking = $bookings->get($paket->id);
                $status = $booking?->status;
                $isActive = $status === TryoutBooking::STATUS_ACTIVE;

                $statusLabel = $isActive
                    ? __('Terdaftar')
                    : __('Tidak terdaftar');

                $canRegister = ! $isActive;

                return [
                    'id' => $paket->id,
                    'booking_id' => $booking?->id,
                    'nama' => $paket->nama,
                    'deskripsi' => $paket->deskripsi ?? __('Belum ada deskripsi paket.'),
                    'harga' => $paket->harga,
                    'waktu_pengerjaan' => $paket->waktu_pengerjaan,
                    'booking_status' => $status,
                    'booking_mulai' => null,
                    'booking_selesai' => null,
                    'durasi_menit' => $paket->waktu_pengerjaan,
                    'bookings_count' => $paket->bookings_count,
                    'status_label' => $statusLabel,
                    'can_register' => $canRegister,
                    'badge' => $isActive ? 'success' : 'neutral',
                ];
            })
            ->values();

        $this->paket = $paketCollection->toArray();

        $this->statistik = [
            'total_paket' => $paketCollection->count(),
            'jadwal_terdekat' => $paketCollection
                ->pluck('booking_mulai')
                ->filter()
                ->sort()
                ->first(),
            'total_selesai' => $paketCollection
                ->where('booking_status', TryoutBooking::STATUS_COMPLETED)
                ->count(),
        ];
    }
}
