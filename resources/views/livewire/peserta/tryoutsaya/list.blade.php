<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="2xl">{{ __('Tryout Saya') }}</flux:heading>
        <flux:text variant="muted">
            {{ __('Pantau semua tryout yang sudah kamu daftar. Mulai dari status pendaftaran, jadwal, hingga riwayat penyelesaian.') }}
        </flux:text>
    </div>

    @if ($bookings)
        <div class="grid gap-4 lg:grid-cols-2">
            @foreach ($bookings as $booking)
                <flux:card class="flex h-full flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <flux:heading size="md">{{ $booking['paket']['nama'] ?? __('Paket Tidak Ditemukan') }}</flux:heading>
                            <flux:badge :color="match ($booking['status']) {
                                \App\Models\TryoutBooking::STATUS_PENDING => 'warning',
                                \App\Models\TryoutBooking::STATUS_ACTIVE => 'success',
                                \App\Models\TryoutBooking::STATUS_COMPLETED => 'primary',
                                \App\Models\TryoutBooking::STATUS_EXPIRED => 'danger',
                                default => 'neutral',
                            }">
                                {{ $booking['status_label'] }}
                            </flux:badge>
                        </div>

                        <flux:text variant="muted">
                            {{ $booking['paket']['deskripsi'] ?? __('Belum ada deskripsi tersedia untuk paket ini.') }}
                        </flux:text>

                        <div class="grid gap-3 text-sm text-zinc-500 dark:text-zinc-300">
                            <div class="flex items-center gap-2">
                                <flux:icon name="clock" class="size-4" />
                                <span>{{ __('Mulai') }}:
                                    <strong class="text-zinc-700 dark:text-white">{{ $booking['tanggal_mulai'] ?? __('Belum dijadwalkan') }}</strong>
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <flux:icon name="calendar" class="size-4" />
                                <span>{{ __('Selesai') }}:
                                    <strong class="text-zinc-700 dark:text-white">{{ $booking['tanggal_selesai'] ?? __('Belum dijadwalkan') }}</strong>
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <flux:icon name="bolt" class="size-4" />
                                <span>{{ __('Durasi') }}:
                                    <strong class="text-zinc-700 dark:text-white">{{ $booking['durasi_menit'] }} {{ __('menit') }}</strong>
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <flux:icon name="currency-dollar" class="size-4" />
                                <span>{{ __('Biaya') }}:
                                    <strong class="text-zinc-700 dark:text-white">{{ number_format($booking['harga'], 0, ',', '.') }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-3">
                        <flux:button variant="ghost" icon="eye" :href="route('peserta.tryout.detail', $booking['paket']['id'])" wire:navigate>
                            {{ __('Lihat Detail Tryout') }}
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-zinc-200 bg-white p-12 text-center dark:border-zinc-700 dark:bg-zinc-900">
            <flux:icon name="calendar-x-mark" class="mx-auto size-10 text-zinc-400" />
            <flux:heading size="lg" class="mt-4">{{ __('Belum ada tryout terdaftar') }}</flux:heading>
            <flux:text variant="muted" class="mt-2">
                {{ __('Kamu belum mendaftarkan diri pada tryout mana pun. Kembali ke halaman Tryout Tersedia untuk memilih paket yang kamu inginkan.') }}
            </flux:text>
            <flux:button class="mt-6" icon="sparkles" :href="route('peserta.tryout-tersedia')" wire:navigate>
                {{ __('Lihat Tryout Tersedia') }}
            </flux:button>
        </div>
    @endif
</div>
