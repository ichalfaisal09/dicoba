<div class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:card>
        <div class="flex flex-col gap-3">
            <flux:heading size="lg">{{ __('Konfigurasi Dasar Baru') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Atur subtes, nama konfigurasi, jumlah soal, urutan tampil, serta nilai minimal kelulusan untuk paket tryout.') }}
            </flux:text>
        </div>

        <form class="mt-6 space-y-6" wire:submit.prevent="store">
            <flux:field>
                <flux:label for="subtes">{{ __('Subtes') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Pilih subtes yang akan dikonfigurasi.') }}
                </flux:text>
                <flux:select id="subtes" wire:model.defer="subtesId" placeholder="{{ __('Pilih subtes') }}">
                    @foreach ($subtesList as $subtes)
                        <option value="{{ $subtes->id }}">{{ $subtes->kode }} — {{ $subtes->nama }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="subtesId" />
            </flux:field>

            <flux:field>
                <flux:label for="nama">{{ __('Nama Konfigurasi') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Gunakan nama deskriptif agar mudah dibedakan.') }}
                </flux:text>
                <flux:input id="nama" type="text" wire:model.defer="nama"
                    placeholder="{{ __('Contoh: TIU - Standar Dasar') }}" />
                <flux:error name="nama" />
            </flux:field>

            <div class="grid gap-4 md:grid-cols-3">
                <flux:field>
                    <flux:label for="jumlah-soal">{{ __('Jumlah Soal') }}</flux:label>
                    <flux:input id="jumlah-soal" type="number" min="1" wire:model.defer="jumlahSoal"
                        placeholder="{{ __('Misal: 30') }}" />
                    <flux:error name="jumlahSoal" />
                </flux:field>

                <flux:field>
                    <flux:label for="urutan">{{ __('Urutan Tampil') }}</flux:label>
                    <flux:input id="urutan" type="number" min="1" wire:model.defer="urutan"
                        placeholder="{{ __('Misal: 1') }}" />
                    <flux:error name="urutan" />
                </flux:field>

                <flux:field>
                    <flux:label for="nilai-minimal">{{ __('Nilai Minimal') }}</flux:label>
                    <flux:input id="nilai-minimal" type="number" min="0" wire:model.defer="nilaiMinimal"
                        placeholder="{{ __('Misal: 300') }}" />
                    <flux:error name="nilaiMinimal" />
                </flux:field>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <flux:button type="submit" icon="check" wire:loading.attr="disabled">
                    {{ __('Simpan Konfigurasi') }}
                </flux:button>
                <flux:button type="button" icon="arrow-uturn-left" variant="ghost"
                    :href="route('admin.konfigurasi')" wire:navigate>
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
        <flux:callout.heading>{{ __('Tips Konfigurasi') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Pastikan urutan unik per paket dan nilai minimal sesuai standar kelulusan yang ditetapkan.') }}
        </flux:callout.text>
    </flux:callout>
</div>
