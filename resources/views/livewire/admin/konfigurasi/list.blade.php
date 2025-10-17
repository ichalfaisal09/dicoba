<div class="flex h-full w-full flex-1 flex-col gap-6">
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

    <flux:card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-1">
                <flux:heading size="lg">{{ __('Konfigurasi Dasar Sistem') }}</flux:heading>
                <flux:text variant="muted">
                    {{ __('Kelola komposisi subtes dalam paket tryout, lengkap dengan jumlah soal, urutan tampil, dan nilai minimal kelulusan.') }}
                </flux:text>
            </div>

            <div class="flex items-center gap-3">
                <flux:button icon="arrow-path" variant="ghost" wire:click="refresh" wire:loading.attr="disabled">
                    {{ __('Muat Ulang') }}
                </flux:button>
                <flux:button icon="plus" :href="route('admin.konfigurasi.create')" wire:navigate>
                    {{ __('Tambah Konfigurasi') }}
                </flux:button>
            </div>
        </div>

        <div class="mt-6 space-y-4">
            @if ($konfigurasi->isEmpty())
                <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-200 bg-zinc-50/60 px-8 py-16 text-center dark:border-white/10 dark:bg-white/5">
                    <flux:icon name="cog-6-tooth" class="size-10 text-zinc-400" />
                    <flux:heading size="md" class="mt-4">{{ __('Belum ada konfigurasi') }}</flux:heading>
                    <flux:text variant="muted" class="mt-2">
                        {{ __('Tambahkan konfigurasi dasar untuk menentukan struktur subtes pada paket tryout.') }}
                    </flux:text>
                </div>
            @else
                <flux:table :paginate="$konfigurasi">
                    <flux:table.columns>
                        <flux:table.column variant="strong">{{ __('Subtes') }}</flux:table.column>
                        <flux:table.column>{{ __('Nama Konfigurasi') }}</flux:table.column>
                        <flux:table.column align="center">{{ __('Jumlah Soal') }}</flux:table.column>
                        <flux:table.column align="center">{{ __('Urutan') }}</flux:table.column>
                        <flux:table.column align="center">{{ __('Nilai Minimal') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Terakhir Diperbarui') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($konfigurasi as $item)
                            <flux:table.row wire:key="konfigurasi-{{ $item->id }}">
                                <flux:table.cell variant="strong">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-semibold text-zinc-800 dark:text-white">
                                            {{ $item->subtes?->nama ?? __('Tidak diketahui') }}
                                        </span>
                                        <flux:badge variant="outline" color="neutral">
                                            {{ $item->subtes?->kode ?? __('N/A') }}
                                        </flux:badge>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:text>{{ $item->nama }}</flux:text>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:badge variant="soft" color="primary">
                                        {{ $item->jumlah_soal }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:badge variant="outline" color="primary">
                                        {{ $item->urutan }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:badge variant="soft" color="neutral">
                                        {{ number_format($item->nilai_minimal) }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <flux:text variant="muted">
                                        {{ optional($item->updated_at)->translatedFormat('d M Y, H:i') }}
                                    </flux:text>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </div>
    </flux:card>
</div>
