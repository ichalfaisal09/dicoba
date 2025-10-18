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
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">{{ __('Import Soal TWK') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Pilih variasi TWK terlebih dahulu, kemudian unggah berkas JSON yang berisi daftar soal.') }}
            </flux:text>
        </div>

        <div class="mt-6 space-y-6">
            <flux:field>
                <flux:label for="variasi">{{ __('Variasi Soal') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Variasi ini akan diterapkan untuk seluruh soal di dalam berkas JSON.') }}
                </flux:text>
                <flux:select id="variasi" wire:model="variasiId" placeholder="{{ __('Pilih variasi TWK') }}">
                    <option value="">{{ __('Pilih Variasi') }}</option>
                    @foreach ($variasiList as $materi => $items)
                        <optgroup label="{{ $materi }}">
                            @foreach ($items as $variasi)
                                <option value="{{ $variasi->id }}">{{ $variasi->nama }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </flux:select>
                <flux:error name="variasiId" />
            </flux:field>

            <flux:field>
                <flux:label for="json">{{ __('Berkas JSON Soal') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Format JSON harus berupa array berisi objek soal dengan atribut teks_soal, tingkat_kesulitan, opsi (A–E), serta opsional pembahasan dan referensi.') }}
                </flux:text>
                <flux:input id="json" type="file" wire:model="jsonFile"
                    accept=".json,application/json,text/plain" />
                <flux:error name="jsonFile" />
            </flux:field>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <flux:button icon="arrow-up-tray" wire:click.prevent="import" wire:loading.attr="disabled">
                {{ __('Import Soal') }}
            </flux:button>
            <flux:button icon="trash" variant="danger" wire:click="resetForm" wire:loading.attr="disabled">
                {{ __('Reset Form') }}
            </flux:button>
            <flux:button icon="arrow-uturn-left" variant="ghost" :href="route('admin.manajemen-soal.twk')" wire:navigate>
                {{ __('Kembali ke Daftar') }}
            </flux:button>
        </div>
    </flux:card>

    <flux:card variant="soft" color="neutral">
        <flux:heading size="md">{{ __('Contoh Struktur JSON') }}</flux:heading>
        <flux:text variant="muted" class="mt-2">
            {{ __('Setiap isi array mewakili satu soal. Huruf opsi akan otomatis diisi A–E apabila tidak disediakan.') }}
        </flux:text>

        <pre class="mt-4 overflow-x-auto rounded-xl border border-zinc-200 bg-zinc-950/95 p-4 text-sm text-zinc-100 dark:border-white/10">
<code>{!! json_encode([
    [
        'teks_soal' => 'Apa dasar negara Indonesia?',
        'tingkat_kesulitan' => 'sedang',
        'opsi' => [
            ['huruf' => 'A', 'teks' => 'Pancasila', 'skor' => 5],
            ['huruf' => 'B', 'teks' => 'UUD 1945', 'skor' => 0],
            ['huruf' => 'C', 'teks' => 'Bhinneka Tunggal Ika', 'skor' => 0],
            ['huruf' => 'D', 'teks' => 'Garuda Pancasila', 'skor' => 0],
            ['huruf' => 'E', 'teks' => 'Proklamasi 1945', 'skor' => 0],
        ],
        'pembahasan' => 'Pancasila merupakan dasar negara Indonesia.',
        'referensi' => 'UU No. 12 Tahun 2011',
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</code>
        </pre>
    </flux:card>
</div>
