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
            <flux:heading size="lg">{{ __('Import Soal TKP') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Pilih variasi TKP terlebih dahulu, kemudian unggah berkas JSON berisi daftar skenario dan opsi penilaian perilaku.') }}
            </flux:text>
        </div>

        <div class="mt-6 space-y-6">
            <flux:field>
                <flux:label for="variasi">{{ __('Variasi Soal') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Variasi akan diterapkan ke seluruh soal dalam berkas import.') }}
                </flux:text>
                <flux:select id="variasi" wire:model="variasiId" placeholder="{{ __('Pilih variasi TKP') }}">
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
                    {{ __('Format JSON berupa array objek soal dengan atribut teks_soal, tingkat_kesulitan, opsi (A–E) beserta skor 0–5, serta opsional pembahasan dan referensi.') }}
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
            <flux:button icon="arrow-uturn-left" variant="ghost" :href="route('admin.manajemen-soal.tkp')"
                wire:navigate>
                {{ __('Kembali ke Daftar') }}
            </flux:button>
        </div>
    </flux:card>

    <flux:card variant="soft" color="neutral">
        <flux:heading size="md">{{ __('Contoh Struktur JSON') }}</flux:heading>
        <flux:text variant="muted" class="mt-2">
            {{ __('Setiap item array mewakili satu skenario TKP dengan lima opsi penilaian. Jika huruf opsi tidak disediakan, sistem akan mengisi otomatis A–E.') }}
        </flux:text>

        <pre
            class="mt-4 overflow-x-auto rounded-xl border border-zinc-200 bg-zinc-950/95 p-4 text-sm text-zinc-100 dark:border-white/10">
<code>{!! json_encode(
    [
        [
            'teks_soal' =>
                'Seorang pegawai baru diminta memimpin rapat penting sementara atasan berhalangan hadir. Apa tindakan paling tepat?',
            'tingkat_kesulitan' => 'sedang',
            'opsi' => [
                ['huruf' => 'A', 'teks' => 'Menunda rapat hingga atasan hadir', 'skor' => 1],
                ['huruf' => 'B', 'teks' => 'Memimpin rapat sesuai agenda dan melaporkan hasilnya', 'skor' => 5],
                ['huruf' => 'C', 'teks' => 'Meminta rekan senior menggantikan', 'skor' => 3],
                ['huruf' => 'D', 'teks' => 'Mengalihkan rapat ke komunikasi daring tanpa koordinasi', 'skor' => 2],
                ['huruf' => 'E', 'teks' => 'Mengabaikan rapat karena bukan tanggung jawabnya', 'skor' => 4],
            ],
            'pembahasan' => 'Memimpin rapat dan melaporkan hasil menunjukkan inisiatif serta tanggung jawab.',
            'referensi' => 'Modul TKP BKN 2025, Bab 3',
        ],
    ],
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
) !!}</code>
        </pre>
    </flux:card>
</div>
