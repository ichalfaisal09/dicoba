<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
        <flux:card>
            <flux:heading size="xl">{{ $paket->nama }}</flux:heading>
            <flux:text variant="muted" class="mt-2">
                {{ $paket->deskripsi ?? __('Belum ada deskripsi detail untuk paket ini.') }}
            </flux:text>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div
                    class="flex items-start gap-3 rounded-xl border border-zinc-200 px-4 py-3 shadow-sm dark:border-zinc-700">
                    <flux:icon name="clock" class="size-6 text-indigo-500" />
                    <div>
                        <p class="text-xs uppercase tracking-wide text-zinc-500">{{ __('Durasi') }}</p>
                        <p class="text-lg font-semibold text-zinc-900 dark:text-white">
                            {{ $paket->waktu_pengerjaan }} {{ __('menit') }}
                        </p>
                    </div>
                </div>
                <div
                    class="flex items-start gap-3 rounded-xl border border-zinc-200 px-4 py-3 shadow-sm dark:border-zinc-700">
                    <flux:icon name="currency-dollar" class="size-6 text-emerald-500" />
                    <div>
                        <p class="text-xs uppercase tracking-wide text-zinc-500">{{ __('Biaya') }}</p>
                        <p class="text-lg font-semibold text-zinc-900 dark:text-white">
                            {{ number_format($paket->harga, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div
                    class="flex items-start gap-3 rounded-xl border border-zinc-200 px-4 py-3 shadow-sm dark:border-zinc-700">
                    <flux:icon name="adjustments-horizontal" class="size-6 text-blue-500" />
                    <div>
                        <p class="text-xs uppercase tracking-wide text-zinc-500">{{ __('Status') }}</p>
                        <p class="text-lg font-semibold text-zinc-900 dark:text-white">
                            {{ __($paket->is_aktif === 'aktif' ? 'Aktif' : 'Nonaktif') }}
                        </p>
                    </div>
                </div>
                <div
                    class="flex items-start gap-3 rounded-xl border border-zinc-200 px-4 py-3 shadow-sm dark:border-zinc-700">
                    <flux:icon name="users" class="size-6 text-purple-500" />
                    <div>
                        <p class="text-xs uppercase tracking-wide text-zinc-500">{{ __('Total Peserta') }}</p>
                        <p class="text-lg font-semibold text-zinc-900 dark:text-white">
                            {{ $paket->bookings()->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                <flux:heading size="lg">{{ __('Struktur Tryout') }}</flux:heading>
                <flux:text variant="muted">
                    {{ __('Tambahkan keterangan struktur paket tryout di sini, misalnya jumlah subtes, bobot penilaian, atau materi yang diujikan.') }}
                </flux:text>

                <flux:callout icon="information-circle">
                    <flux:callout.heading>
                        {{ __('Butuh bantuan?') }}
                    </flux:callout.heading>
                    <flux:callout.text>
                        {{ __('Hubungi mentor atau admin jika memerlukan info tambahan mengenai paket ini.') }}
                    </flux:callout.text>
                </flux:callout>
            </div>
        </flux:card>

        <div class="flex flex-col gap-4">
            <flux:card>
                <flux:heading size="lg" class="flex items-center gap-2">
                    <flux:icon name="user-circle" class="size-5" />
                    {{ __('Status Pendaftaran Kamu') }}
                </flux:heading>

                @if ($booking)
                    <div class="mt-4 space-y-3 text-sm">
                        <flux:badge variant="soft" color="primary">
                            {{ __($booking->status) }}
                        </flux:badge>
                        <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-300">
                            <flux:icon name="clock" class="size-4" />
                            <span>{{ __('Mulai') }}:
                                <strong class="text-zinc-700 dark:text-white">
                                    {{ optional($booking->tanggal_mulai)->translatedFormat('d M Y, H:i') ?? __('Belum dijadwalkan') }}
                                </strong>
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-300">
                            <flux:icon name="calendar" class="size-4" />
                            <span>{{ __('Selesai') }}:
                                <strong class="text-zinc-700 dark:text-white">
                                    {{ optional($booking->tanggal_selesai)->translatedFormat('d M Y, H:i') ?? __('Belum dijadwalkan') }}
                                </strong>
                            </span>
                        </div>
                    </div>
                @else
                    <flux:text variant="muted" class="mt-4">
                        {{ __('Kamu belum mendaftar pada paket ini. Kembali ke daftar tryout untuk melakukan pendaftaran.') }}
                    </flux:text>
                @endif

                <div class="mt-6 flex flex-col gap-3">
                    <flux:button icon="arrow-uturn-left" :href="route('peserta.tryout-tersedia')" wire:navigate>
                        {{ __('Kembali ke Daftar Tryout') }}
                    </flux:button>
                    <flux:button variant="ghost" icon="chat-bubble-bottom-center-text">
                        {{ __('Diskusi dengan Mentor') }}
                    </flux:button>
                </div>
            </flux:card>
        </div>
    </div>
</div>
