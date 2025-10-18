<div class="flex h-full w-full flex-1 flex-col gap-6">
    @if ($callout = session('callout'))
        <flux:callout :icon="$callout['icon'] ?? 'bell'" :variant="trim($callout['variant'] ?? 'secondary')"
            class="mb-4" inline x-data="{ visible: true }" x-show="visible">
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
                <flux:heading size="lg">{{ __('Daftar Soal TWK') }}</flux:heading>
                <flux:text variant="muted">
                    {{ __('Kelola pertanyaan TWK. Daftar berikut hanya menampilkan teks soal untuk memudahkan penelusuran.') }}
                </flux:text>
            </div>

            <div class="flex items-center gap-3">
                <flux:button icon="arrow-path" variant="ghost" wire:click="refresh" wire:loading.attr="disabled">
                    {{ __('Muat Ulang') }}
                </flux:button>
                <flux:button icon="arrow-up-tray" variant="ghost" :href="route('admin.manajemen-soal.twk.import')"
                    wire:navigate>
                    {{ __('Import JSON') }}
                </flux:button>
                <flux:button icon="plus" :href="route('admin.manajemen-soal.twk.create')" wire:navigate>
                    {{ __('Tambah Soal') }}
                </flux:button>
            </div>
        </div>

        <div class="mt-6 space-y-">
            @if ($soal->isEmpty())
                <div
                    class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-200 bg-zinc-50/60 px-8 py-16 text-center dark:border-white/10 dark:bg-white/5">
                    <flux:icon name="document-text" class="size-10 text-zinc-400" />
                    <flux:heading size="md" class="mt-4">{{ __('Belum ada soal TWK') }}</flux:heading>
                    <flux:text variant="muted" class="mt-2">
                        {{ __('Tambahkan soal TWK terlebih dahulu untuk mulai mengelolanya.') }}
                    </flux:text>
                </div>
            @else
                <flux:table :paginate="$soal">
                    <flux:table.columns>
                        <flux:table.column variant="strong">{{ __('Teks Soal') }}</flux:table.column>
                        <flux:table.column class="w-40 text-right">{{ __('Aksi') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($soal as $item)
                            <flux:table.row wire:key="soal-{{ $item->id }}">
                                <flux:table.cell>
                                    <flux:text>{{ $item->teks_soal }}</flux:text>
                                </flux:table.cell>
                                <flux:table.cell class="flex justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="eye"
                                        :href="route('admin.manajemen-soal.twk') . '/show/' . $item->id" wire:navigate>
                                        {{ __('Detail') }}
                                    </flux:button>
                                    <flux:button size="sm" variant="danger" icon="trash"
                                        wire:click="confirmDeletion({{ $item->id }})" wire:loading.attr="disabled">
                                        {{ __('Hapus') }}
                                    </flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </div>
    </flux:card>
</div>
