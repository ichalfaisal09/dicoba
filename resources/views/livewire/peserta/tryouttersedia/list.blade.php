<div class="flex h-full w-full flex-1 flex-col gap-8">
    @if ($callout = session('callout'))
        <flux:callout :icon="$callout['icon'] ?? 'bell'" :variant="$callout['variant'] ?? 'secondary'" class="mb-4"
            inline x-data="{ visible: true }" x-show="visible">
            <flux:callout.heading class="flex gap-2 @max-md:flex-col items-start">
                {{ $callout['heading'] ?? 'Informasi' }}
                @if (!empty($callout['text']))
                    <flux:text>{{ $callout['text'] }}</flux:text>
                @endif
            </flux:callout.heading>

            @if (!empty($callout['content']))
                <flux:callout.text>{{ $callout['content'] }}</flux:callout.text>
            @endif

            <x-slot name="controls">
                <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
            </x-slot>
        </flux:callout>
    @endif

    <div class="relative overflow-hidden rounded-2xl border border-zinc-200 bg-gradient-to-br from-indigo-50 via-white to-blue-50 p-8 dark:border-zinc-700 dark:from-indigo-950/60 dark:via-zinc-900 dark:to-blue-950/40">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="space-y-3">
                <flux:badge variant="outline" color="primary">
                    {{ __('Tryout Terbaru Siap Diikuti') }}
                </flux:badge>
                <flux:heading size="2xl">{{ __('Tingkatkan peluang kelulusanmu dengan latihan terstruktur') }}</flux:heading>
                <flux:text class="max-w-2xl" variant="muted">
                    {{ __('Ikuti paket tryout pilihan yang dirancang oleh mentor berpengalaman. Dapatkan analisis hasil, ranking real-time, dan rekomendasi belajar personal.') }}
                </flux:text>
                <div class="flex flex-wrap gap-3">
                    <flux:button icon="play" :href="route('peserta.tryout-tersedia')" wire:navigate>
                        {{ __('Mulai Tryout Sekarang') }}
                    </flux:button>
                    <flux:button variant="ghost" icon="information-circle">
                        {{ __('Pelajari Cara Kerja Tryout') }}
                    </flux:button>
                </div>
            </div>
            <div class="relative w-full max-w-sm self-end md:self-center">
                <div class="rounded-2xl border border-indigo-200 bg-white/70 p-6 shadow-xl ring-1 ring-indigo-200/60 dark:border-indigo-500/30 dark:bg-indigo-950/40 dark:ring-indigo-500/20">
                    <flux:heading size="lg" class="flex items-center gap-2">
                        <flux:icon name="chart-bar" class="size-6 text-indigo-500" />
                        {{ __('Statistik Aktivitas') }}
                    </flux:heading>
                    <div class="mt-4 flex flex-col gap-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500 dark:text-zinc-300">{{ __('Tryout aktif') }}</span>
                            <span class="font-semibold text-indigo-600 dark:text-indigo-300">{{ count($paket) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500 dark:text-zinc-300">{{ __('Jadwal terdekat') }}</span>
                            <span class="font-semibold">{{ $paket[0]['mulai'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500 dark:text-zinc-300">{{ __('Total latihan selesai') }}</span>
                            <span class="font-semibold">12</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <flux:heading size="lg">{{ __('Daftar Paket Tryout') }}</flux:heading>
                <flux:text variant="muted">
                    {{ __('Pilih paket yang sesuai dengan kebutuhan belajarmu. Setiap paket dilengkapi pembahasan dan laporan nilai.') }}
                </flux:text>
            </div>
            <div class="flex flex-wrap gap-3">
                <flux:input icon="magnifying-glass" placeholder="{{ __('Cari berdasarkan nama paket') }}" class="w-full md:w-64" />
                <flux:select placeholder="{{ __('Urutkan') }}">
                    <option value="latest">{{ __('Terbaru') }}</option>
                    <option value="upcoming">{{ __('Jadwal terdekat') }}</option>
                    <option value="popular">{{ __('Paling populer') }}</option>
                </flux:select>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            @forelse ($paket as $item)
                <div class="flex h-full flex-col justify-between rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <flux:badge variant="soft" :color="$item['badge'] ?? 'neutral'">
                                {{ $item['status'] }}
                            </flux:badge>
                            <flux:icon name="calendar-days" class="size-5 text-indigo-500" />
                        </div>
                        <flux:heading size="md">{{ $item['nama'] }}</flux:heading>
                        <flux:text variant="muted">{{ $item['deskripsi'] }}</flux:text>
                    </div>

                    <div class="mt-6 space-y-3 text-sm">
                        <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-300">
                            <flux:icon name="clock" class="size-5" />
                            <span>{{ __('Mulai') }}: <strong class="text-zinc-700 dark:text-white">{{ $item['mulai'] }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-300">
                            <flux:icon name="calendar" class="size-5" />
                            <span>{{ __('Selesai') }}: <strong class="text-zinc-700 dark:text-white">{{ $item['selesai'] }}</strong></span>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-3">
                        <flux:button icon="sparkles" block>
                            {{ __('Ikuti Sekarang') }}
                        </flux:button>
                        <flux:button variant="ghost" icon="eye" block>
                            {{ __('Lihat Detail Paket') }}
                        </flux:button>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-zinc-200 bg-white p-10 text-center dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:icon name="inbox" class="mx-auto size-10 text-zinc-400" />
                    <flux:heading size="md" class="mt-4">{{ __('Belum ada tryout tersedia') }}</flux:heading>
                    <flux:text variant="muted" class="mt-2">
                        {{ __('Pantau halaman ini secara berkala untuk mendapatkan informasi tryout terbaru.') }}
                    </flux:text>
                </div>
            @endforelse
        </div>
    </div>
</div>
