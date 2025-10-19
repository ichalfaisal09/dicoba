<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="2xl">{{ __('Tryout Saya') }}</flux:heading>
        <flux:text variant="muted">
            {{ __('Pantau semua tryout yang sudah kamu daftar. Mulai dari status pendaftaran, jadwal, hingga riwayat penyelesaian.') }}
        </flux:text>
    </div>

    <flux:card class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div class="grid w-full gap-3 md:grid-cols-[minmax(0,1fr)_minmax(0,200px)]">
            <flux:input icon="magnifying-glass" placeholder="{{ __('Cari nama paket tryout') }}" wire:model.live.debounce.400ms="search" />

            <flux:select placeholder="{{ __('Semua Status') }}" wire:model.live="filterStatus">
                <flux:select.option value="all">{{ __('Semua Status') }}</flux:select.option>
                <flux:select.option value="pending">{{ __('Menunggu konfirmasi') }}</flux:select.option>
                <flux:select.option value="active">{{ __('Sedang berlangsung') }}</flux:select.option>
                <flux:select.option value="completed">{{ __('Selesai') }}</flux:select.option>
                <flux:select.option value="expired">{{ __('Kedaluwarsa') }}</flux:select.option>
            </flux:select>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" icon="arrow-path" wire:click="refreshBookings" wire:loading.attr="disabled">
                {{ __('Segarkan') }}
            </flux:button>
        </div>
    </flux:card>

    @if ($filteredBookings)
        <div class="grid gap-4 lg:grid-cols-2">
            @foreach ($filteredBookings as $booking)
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
                                <flux:icon name="calendar-days" class="size-4" />
                                <span>{{ __('Terdaftar sejak') }}:
                                    <strong class="text-zinc-700 dark:text-white">{{ $booking['terdaftar'] ?? __('-') }}</strong>
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
                        @if ($booking['status'] === \App\Models\TryoutBooking::STATUS_ACTIVE)
                            <flux:button icon="play" :href="route('peserta.tryout.ujian', $booking['id'])" wire:navigate>
                                {{ __('Mulai Ujian') }}
                            </flux:button>
                            @if ($booking['has_progress'])
                                <flux:button variant="outline" icon="arrow-path" :href="route('peserta.tryout.detail', $booking['paket']['id'])" wire:navigate>
                                    {{ __('Lanjutkan Tryout') }}
                                </flux:button>
                            @endif
                            <flux:button variant="ghost" icon="eye" :href="route('peserta.tryout.detail', $booking['paket']['id'])" wire:navigate>
                                {{ __('Lihat Detail Tryout') }}
                            </flux:button>
                        @elseif ($booking['status'] === \App\Models\TryoutBooking::STATUS_COMPLETED)
                            <flux:button icon="chart-bar" :href="route('peserta.tryout.hasil', $booking['id'])" wire:navigate>
                                {{ __('Lihat Hasil & Pembahasan') }}
                            </flux:button>
                        @else
                            <flux:button variant="ghost" icon="eye" :href="route('peserta.tryout.detail', $booking['paket']['id'])" wire:navigate>
                                {{ __('Lihat Detail Tryout') }}
                            </flux:button>
                        @endif
                    </div>
                </flux:card>
            @endforeach
        </div>
    @elseif ($bookings)
        <div class="rounded-2xl border border-dashed border-amber-200 bg-amber-50 p-12 text-center dark:border-amber-500/40 dark:bg-amber-900/20">
            <flux:icon name="magnifying-glass" class="mx-auto size-10 text-amber-500" />
            <flux:heading size="lg" class="mt-4">{{ __('Tidak ada tryout yang cocok') }}</flux:heading>
            <flux:text variant="muted" class="mt-2">
                {{ __('Coba ubah kata kunci pencarian atau pilih status lain untuk melihat daftar tryout yang sesuai.') }}
            </flux:text>
            <flux:button class="mt-6" icon="adjustments-horizontal" wire:click="refreshBookings">
                {{ __('Reset Filter') }}
            </flux:button>
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-zinc-200 bg-white p-12 text-center dark:border-zinc-700 dark:bg-zinc-900">
            <flux:icon name="calendar-days" class="mx-auto size-10 text-zinc-400" />
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
