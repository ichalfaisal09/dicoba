<div class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:card>
        <div class="flex flex-col gap-3">
            <flux:heading size="lg">{{ __('Variasi Baru') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Tambahkan variasi untuk materi terpilih. Setiap baris pada textarea akan menjadi satu variasi dengan kode otomatis.') }}
            </flux:text>
        </div>

        <form class="mt-6 space-y-6" wire:submit.prevent="store">
            <flux:field>
                <flux:label for="subtes">{{ __('Subtes') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Pilih subtes terlebih dahulu untuk memfilter daftar materi.') }}
                </flux:text>
                <flux:select id="subtes" wire:model.live="subtesId" placeholder="{{ __('Pilih subtes') }}">
                    @foreach ($subtesList as $subtes)
                        <option value="{{ $subtes->id }}">{{ $subtes->kode }} — {{ $subtes->nama }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="subtesId" />
            </flux:field>

            <flux:field>
                <flux:label for="materi">{{ __('Materi') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Pilih materi yang akan memiliki variasi baru. Kode variasi mengikuti kode materi.') }}
                </flux:text>
                <flux:select id="materi" wire:model.live="materiId" placeholder="{{ __('Pilih materi') }}" :disabled="empty($subtesId)">
                    @foreach ($materiList as $materi)
                        <option value="{{ $materi->id }}">{{ $materi->kode }} — {{ $materi->nama }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="materiId" />
            </flux:field>

            <flux:field>
                <flux:label for="entries">{{ __('Nama Variasi') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Masukkan satu variasi per baris. Deskripsi akan terisi otomatis menggunakan kode dan nama variasi.') }}
                </flux:text>
                <flux:textarea id="entries" wire:model.defer="entries" rows="8" resize="vertical"
                    placeholder="Contoh:
Paket Ujian Utama
Latihan Cepat
Simulasi Lengkap" />
                <flux:error name="entries" />
            </flux:field>

            <div class="flex flex-wrap items-center gap-3">
                <flux:button type="submit" icon="check" wire:loading.attr="disabled">
                    {{ __('Simpan Variasi') }}
                </flux:button>
                <flux:button type="button" icon="arrow-uturn-left" variant="ghost"
                    :href="route('admin.kategorisasi.variasi')" wire:navigate>
                    {{ __('Batal') }}
                </flux:button>
                <flux:button type="button" icon="trash" variant="danger" wire:click="resetForm"
                    wire:loading.attr="disabled">
                    {{ __('Reset Form') }}
                </flux:button>
            </div>
        </form>
    </flux:card>

    <flux:callout icon="information-circle" variant="secondary">
        <flux:callout.heading>{{ __('Tips Variasi') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Gunakan nama variasi yang menggambarkan tipe soal atau skenario latihan agar tim mudah mengelola konten.') }}
        </flux:callout.text>
    </flux:callout>
</div>
