<div class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-1">
                <flux:heading size="lg">{{ __('Daftar Materi') }}</flux:heading>
                <flux:text variant="muted">
                    {{ __('Kelola materi berdasarkan subtes untuk menjaga konsistensi konten tryout.') }}
                </flux:text>
            </div>

            <div class="flex items-center gap-3">
                <flux:button icon="arrow-path" variant="ghost" wire:click="refresh" wire:loading.attr="disabled">
                    {{ __('Muat Ulang') }}
                </flux:button>
                <flux:button icon="plus" :href="route('admin.kategorisasi.materi.create')" wire:navigate>
                    {{ __('Tambah Materi') }}
                </flux:button>
            </div>
        </div>

        <div class="mt-6">
            @if ($materi->isEmpty())
                <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-200 bg-zinc-50/60 px-8 py-16 text-center dark:border-white/10 dark:bg-white/5">
                    <flux:icon name="book-open" class="size-10 text-zinc-400" />
                    <flux:heading size="md" class="mt-4">{{ __('Belum ada materi') }}</flux:heading>
                    <flux:text variant="muted" class="mt-2">
                        {{ __('Tambahkan materi baru untuk memperkaya proses belajar.') }}
                    </flux:text>
                    <flux:button icon="plus" class="mt-6" :href="route('admin.kategorisasi.materi.create')" wire:navigate>
                        {{ __('Tambah Materi Pertama') }}
                    </flux:button>
                </div>
            @else
                <flux:table :paginate="$materi">
                    <flux:table.columns>
                        <flux:table.column variant="strong">{{ __('Materi') }}</flux:table.column>
                        <flux:table.column>{{ __('Subtes') }}</flux:table.column>
                        <flux:table.column>{{ __('Kode') }}</flux:table.column>
                        <flux:table.column>{{ __('Deskripsi') }}</flux:table.column>
                        <flux:table.column align="center">{{ __('Variasi') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Terakhir Diperbarui') }}</flux:table.column>
                        <flux:table.column align="center">{{ __('Aksi') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($materi as $item)
                            <flux:table.row wire:key="materi-{{ $item->id }}">
                                <flux:table.cell variant="strong">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-semibold text-zinc-800 dark:text-white">{{ $item->nama }}</span>
                                        <flux:badge>{{ __('ID: :id', ['id' => $item->id]) }}</flux:badge>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge variant="soft" color="neutral">
                                        {{ $item->subtes?->nama ?? __('Tidak diketahui') }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge variant="outline" color="neutral">{{ strtoupper($item->kode) }}</flux:badge>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:text variant="muted" class="line-clamp-2 max-w-xs">
                                        {{ $item->deskripsi ?? __('Belum ada deskripsi') }}
                                    </flux:text>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:badge variant="outline" color="neutral">
                                        {{ number_format($item->variasi_count ?? 0) }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <flux:text variant="muted">
                                        {{ optional($item->updated_at)->translatedFormat('d M Y, H:i') }}
                                    </flux:text>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <div class="flex items-center justify-center gap-2">
                                        <flux:button icon="pencil-square" size="sm" variant="ghost"
                                            wire:click="edit({{ $item->id }})" wire:loading.attr="disabled">
                                            {{ __('Ubah') }}
                                        </flux:button>
                                        <flux:button icon="trash" size="sm" variant="danger"
                                            wire:click="confirmDeletion({{ $item->id }})" wire:loading.attr="disabled">
                                            {{ __('Hapus') }}
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
