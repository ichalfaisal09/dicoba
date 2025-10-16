<div class="flex h-full w-full flex-1 flex-col gap-6">
        <flux:card>
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Daftar Subtes') }}</flux:heading>
                    <flux:text variant="muted">
                        {{ __('Kelola kategori subtes beserta detail kode, nama, dan deskripsi singkatnya.') }}
                    </flux:text>
                </div>

                <div class="flex items-center gap-3">
                    <flux:button icon="arrow-path" variant="ghost" wire:click="refresh" wire:loading.attr="disabled">
                        {{ __('Muat Ulang') }}
                    </flux:button>
                    <flux:button icon="plus" :href="route('admin.kategorisasi.subtes.create')" wire:navigate>
                        {{ __('Tambah Subtes') }}
                    </flux:button>
                </div>
            </div>

            <div class="mt-6">
                @if ($subtes->isEmpty())
                    <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-200 bg-zinc-50/60 px-8 py-16 text-center dark:border-white/10 dark:bg-white/5">
                        <flux:icon name="list-bullet" class="size-10 text-zinc-400" />
                        <flux:heading size="md" class="mt-4">{{ __('Belum ada data subtes') }}</flux:heading>
                        <flux:text variant="muted" class="mt-2">
                            {{ __('Mulai dengan menambahkan subtes baru untuk menyusun hirarki materi tryout.') }}
                        </flux:text>
                        <flux:button icon="plus" class="mt-6" :href="route('admin.kategorisasi.subtes.create')" wire:navigate>
                            {{ __('Tambah Subtes Pertama') }}
                        </flux:button>
                    </div>
                @else
                    <flux:table :paginate="$subtes">
                        <flux:table.columns>
                            <flux:table.column variant="strong">{{ __('Subtes') }}</flux:table.column>
                            <flux:table.column>{{ __('Kode') }}</flux:table.column>
                            <flux:table.column>{{ __('Deskripsi') }}</flux:table.column>
                            <flux:table.column align="center">{{ __('Materi') }}</flux:table.column>
                            <flux:table.column align="end">{{ __('Terakhir Diperbarui') }}</flux:table.column>
                            <flux:table.column align="center">{{ __('Aksi') }}</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach ($subtes as $item)
                                <flux:table.row wire:key="subtes-{{ $item->id }}">
                                    <flux:table.cell variant="strong">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-semibold text-zinc-800 dark:text-white">{{ $item->nama }}</span>
                                            <flux:badge>{{ __('ID: :id', ['id' => $item->id]) }}</flux:badge>
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <flux:badge variant="soft" color="zinc">{{ strtoupper($item->kode) }}</flux:badge>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <flux:text variant="muted" class="line-clamp-2 max-w-xs">
                                            {{ $item->deskripsi ?? __('Belum ada deskripsi') }}
                                        </flux:text>
                                    </flux:table.cell>

                                    <flux:table.cell align="center">
                                        <flux:badge variant="outline" color="neutral">
                                            {{ number_format($item->materi_count ?? 0) }}
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
                                                wire:click="confirmDeletion({{ $item->id }})"
                                                wire:loading.attr="disabled">
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
