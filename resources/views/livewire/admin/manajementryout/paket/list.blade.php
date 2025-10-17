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
                <flux:heading size="lg">{{ __('Daftar Paket Tryout') }}</flux:heading>
                <flux:text variant="muted">
                    {{ __('Kelola paket tryout beserta konfigurasi subtes, durasi pengerjaan, harga, dan status penayangan.') }}
                </flux:text>
            </div>

            <div class="flex items-center gap-3">
                <flux:button icon="arrow-path" variant="ghost" wire:click="refresh" wire:loading.attr="disabled">
                    {{ __('Muat Ulang') }}
                </flux:button>
                <flux:button icon="plus" :href="route('admin.manajemen-tryout.paket.create')" wire:navigate>
                    {{ __('Tambah Paket') }}
                </flux:button>
            </div>
        </div>

        <div class="mt-6 space-y-">
            @if ($paket->isEmpty())
                <div
                    class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-200 bg-zinc-50/60 px-8 py-16 text-center dark:border-white/10 dark:bg-white/5">
                    <flux:icon name="queue-list" class="size-10 text-zinc-400" />
                    <flux:heading size="md" class="mt-4">{{ __('Belum ada paket tryout') }}</flux:heading>
                    <flux:text variant="muted" class="mt-2">
                        {{ __('Susun paket tryout berdasarkan konfigurasi dasar sistem untuk mulai menawarkan tryout kepada peserta.') }}
                    </flux:text>
                </div>
            @else
                <flux:table :paginate="$paket">
                    <flux:table.columns>
                        <flux:table.column variant="strong">{{ __('Paket') }}</flux:table.column>
                        <flux:table.column>{{ __('Konfigurasi') }}</flux:table.column>
                        <flux:table.column align="center">{{ __('Durasi') }}</flux:table.column>
                        <flux:table.column align="center">{{ __('Harga') }}</flux:table.column>
                        <flux:table.column align="center">{{ __('Status') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Terakhir Diperbarui') }}</flux:table.column>
                        <flux:table.column align="center">{{ __('Aksi') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($paket as $item)
                            <flux:table.row wire:key="paket-{{ $item->id }}">
                                <flux:table.cell variant="strong">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-sm font-semibold text-zinc-800 dark:text-white">{{ $item->nama }}</span>
                                        <flux:badge variant="outline" color="neutral">
                                            ID: {{ $item->id }}
                                        </flux:badge>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <div class="flex flex-col gap-2">
                                        @forelse ($item->konfigurasiDasar as $konfigurasi)
                                            <div class="flex flex-col rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-white/10">
                                                <span class="font-medium text-zinc-800 dark:text-white">
                                                    {{ $konfigurasi->nama }}
                                                </span>
                                                <flux:text variant="muted" size="sm">
                                                    {{ __('Subtes: :nama', ['nama' => $konfigurasi->subtes?->nama ?? __('Tidak diketahui')]) }}
                                                </flux:text>
                                                <div class="flex flex-wrap gap-1">
                                                    <flux:badge variant="soft" color="neutral">
                                                        {{ __('Urutan :urutan', ['urutan' => $konfigurasi->pivot->urutan]) }}
                                                    </flux:badge>
                                                    <flux:badge variant="soft" color="neutral">
                                                        {{ __(':jumlah soal', ['jumlah' => $konfigurasi->jumlah_soal]) }}
                                                    </flux:badge>
                                                    <flux:badge variant="soft" color="neutral">
                                                        {{ $konfigurasi->subtes?->kode ?? __('N/A') }}
                                                    </flux:badge>
                                                </div>
                                            </div>
                                        @empty
                                            <flux:text variant="muted">{{ __('Belum ada konfigurasi') }}</flux:text>
                                        @endforelse
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:badge variant="soft" color="primary">
                                        {{ $item->waktu_pengerjaan }} {{ __('menit') }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:text>
                                        {{ __('Rp :harga', ['harga' => number_format($item->harga, 0, ',', '.')]) }}
                                    </flux:text>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:badge :color="$item->is_aktif === 'aktif' ? 'success' : 'neutral'"
                                        variant="soft">
                                        {{ __($item->is_aktif === 'aktif' ? 'Aktif' : 'Nonaktif') }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <flux:text variant="muted">
                                        {{ optional($item->updated_at)->translatedFormat('d M Y, H:i') }}
                                    </flux:text>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <div class="flex items-center justify-center gap-2">
                                        <flux:button icon="adjustments-horizontal" size="sm" variant="ghost"
                                            wire:click="toggleStatus({{ $item->id }})"
                                            wire:loading.attr="disabled">
                                            {{ __($item->is_aktif === 'aktif' ? 'Nonaktifkan' : 'Aktifkan') }}
                                        </flux:button>
                                        <flux:button icon="pencil-square" size="sm" variant="ghost" disabled>
                                            {{ __('Ubah') }}
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </div>
    </flux:card>
</div>
