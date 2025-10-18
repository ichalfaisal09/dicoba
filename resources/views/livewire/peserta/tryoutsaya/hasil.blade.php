<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="2xl">{{ __('Hasil Tryout') }}</flux:heading>
        <flux:text variant="muted">
            {{ __('Tinjau performa kamu pada tryout ini dan pelajari pembahasan tiap soal untuk meningkatkan persiapan.') }}
        </flux:text>
    </div>

    <flux:card class="flex flex-col gap-4">
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">{{ $detail['paket']['nama'] ?? __('Paket Tidak Ditemukan') }}</flux:heading>
            <flux:text variant="muted">
                {{ $detail['paket']['deskripsi'] ?? __('Belum ada deskripsi untuk paket ini.') }}
            </flux:text>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <flux:card class="bg-indigo-50/80 p-4 text-indigo-900 dark:bg-indigo-500/10 dark:text-indigo-200">
                <flux:heading size="sm">{{ __('Skor Total') }}</flux:heading>
                <p class="mt-2 text-2xl font-semibold">
                    {{ $detail['skor_total'] ?? __('Belum tersedia') }}
                </p>
            </flux:card>

            <flux:card class="bg-emerald-50/80 p-4 text-emerald-900 dark:bg-emerald-500/10 dark:text-emerald-200">
                <flux:heading size="sm">{{ __('Peringkat') }}</flux:heading>
                <p class="mt-2 text-2xl font-semibold">
                    {{ $detail['peringkat'] ?? __('Belum tersedia') }}
                </p>
            </flux:card>

            <flux:card class="bg-blue-50/80 p-4 text-blue-900 dark:bg-blue-500/10 dark:text-blue-200">
                <flux:heading size="sm">{{ __('Jawaban Benar') }}</flux:heading>
                <p class="mt-2 text-2xl font-semibold">
                    {{ $detail['jawaban_benar'] ?? 0 }}
                </p>
            </flux:card>

            <flux:card class="bg-rose-50/80 p-4 text-rose-900 dark:bg-rose-500/10 dark:text-rose-200">
                <flux:heading size="sm">{{ __('Jawaban Salah') }}</flux:heading>
                <p class="mt-2 text-2xl font-semibold">
                    {{ $detail['jawaban_salah'] ?? 0 }}
                </p>
            </flux:card>
        </div>

        <div class="flex gap-3">
            <flux:button icon="arrow-uturn-left" :href="route('peserta.tryout-saya')" wire:navigate>
                {{ __('Kembali ke Tryout Saya') }}
            </flux:button>
            <flux:button variant="ghost" icon="sparkles" :href="route('peserta.tryout-tersedia')" wire:navigate>
                {{ __('Cari Tryout Lain') }}
            </flux:button>
        </div>
    </flux:card>

    <flux:card>
        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ __('Pembahasan Soal') }}</flux:heading>
            <flux:badge>{{ __('Daftar Soal') }}</flux:badge>
        </div>

        @if (!empty($detail['pembahasan']))
            <div class="mt-6 space-y-4">
                @foreach ($detail['pembahasan'] as $index => $pembahasan)
                    <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <flux:heading size="sm">{{ __('Soal :number', ['number' => $index + 1]) }}</flux:heading>
                                <flux:badge :color="$pembahasan['benar'] ?? false ? 'success' : 'danger'">
                                    {{ ($pembahasan['benar'] ?? false) ? __('Benar') : __('Salah') }}
                                </flux:badge>
                            </div>

                            <flux:text>
                                {{ $pembahasan['penjelasan'] ?? __('Belum ada pembahasan untuk soal ini.') }}
                            </flux:text>

                            @if (!empty($pembahasan['referensi']))
                                <flux:callout variant="muted" icon="book-open">
                                    <flux:callout.text>
                                        {{ __('Referensi: :referensi', ['referensi' => $pembahasan['referensi']]) }}
                                    </flux:callout.text>
                                </flux:callout>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="mt-6 rounded-xl border border-dashed border-zinc-200 bg-zinc-50 p-6 text-center dark:border-zinc-700 dark:bg-zinc-900/40">
                <flux:icon name="clipboard-document-list" class="mx-auto size-10 text-zinc-400" />
                <flux:heading size="md" class="mt-4">{{ __('Pembahasan belum tersedia') }}</flux:heading>
                <flux:text variant="muted" class="mt-2">
                    {{ __('Pembahasan untuk tryout ini belum diunggah. Silakan kembali lagi nanti atau hubungi mentor.') }}
                </flux:text>
            </div>
        @endif
    </flux:card>
</div>
