<div class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:card>
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">{{ __('Generator Contoh JSON') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Pilih variasi TWK untuk menyiapkan contoh struktur JSON yang sesuai.') }}
            </flux:text>
        </div>

        <div class="mt-6 space-y-6">
            <flux:field>
                <flux:label for="variasi">{{ __('Variasi Soal') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Variasi hanya menampilkan subtes TWK dan akan menjadi dasar pembuatan template JSON.') }}
                </flux:text>
                <flux:select id="variasi" wire:model="variasiId" placeholder="{{ __('Pilih variasi TWK') }}">
                    <option value="">{{ __('Pilih Variasi') }}</option>
                    @foreach ($variasiList as $materi => $items)
                        <optgroup label="{{ $materi }}">
                            @foreach ($items as $variasi)
                                <option value="{{ $variasi['id'] ?? ($variasi['value'] ?? $variasi->id) }}">
                                    {{ $variasi['nama'] ?? ($variasi['label'] ?? $variasi->nama) }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label for="jumlah-template">{{ __('Jumlah Template') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Tentukan berapa banyak contoh soal yang ingin digenerasikan (default 10).') }}
                </flux:text>
                <flux:input id="jumlah-template" type="number" min="1" wire:model="jumlahTemplate"
                    suffix="{{ __('item') }}" />
            </flux:field>

            <div class="flex flex-wrap items-center gap-3">
                <flux:button icon="sparkles" wire:click="generate" wire:loading.attr="disabled">
                    {{ __('Generate Sekarang') }}
                </flux:button>
                <flux:button icon="arrow-down-tray" variant="ghost" wire:click="download" wire:loading.attr="disabled">
                    {{ __('Download JSON') }}
                </flux:button>
            </div>

            <flux:field>
                <flux:label for="hasil-generate">{{ __('Hasil Generate') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Salin hasil berikut untuk digunakan sebagai contoh import JSON.') }}
                </flux:text>
                <flux:textarea id="hasil-generate" wire:model="hasilGenerate" rows="12" readonly></flux:textarea>
            </flux:field>
        </div>
    </flux:card>
</div>
