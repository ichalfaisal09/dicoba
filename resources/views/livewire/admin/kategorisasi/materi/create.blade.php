<div class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:card>
        <div class="flex flex-col gap-3">
            <flux:heading size="lg">{{ __('Materi Baru') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Tambahkan materi untuk subtes tertentu. Isikan kode unik, nama materi, pilih subtes, dan deskripsi singkat.') }}
            </flux:text>
        </div>

        <form class="mt-6 space-y-6" wire:submit.prevent="store">
            <flux:field>
                <flux:label for="subtes">{{ __('Subtes') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Pilih subtes tempat materi ini akan ditempatkan.') }}
                </flux:text>
                <flux:select id="subtes" wire:model.defer="subtesId" placeholder="{{ __('Pilih subtes') }}">
                    @foreach ($subtesList as $subtes)
                        <option value="{{ $subtes->id }}">{{ $subtes->kode }} — {{ $subtes->nama }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="subtesId" />
            </flux:field>

            <flux:field>
                <flux:label for="entries">{{ __('Nama Materi') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Gunakan satu baris per materi. Kode akan dihasilkan otomatis, dan deskripsi akan menggabungkan kode serta nama materi.') }}
                </flux:text>
                <flux:textarea id="entries" wire:model.defer="entries" rows="8" resize="vertical"
                    placeholder="Contoh:
Penalaran Verbal Dasar
Sinonim dan Antonim
Analogi Verbal" />
                <flux:error name="entries" />
            </flux:field>

            <div class="flex flex-wrap items-center gap-3">
                <flux:button type="submit" icon="check" wire:loading.attr="disabled">
                    {{ __('Simpan Materi') }}
                </flux:button>
                <flux:button type="button" icon="arrow-uturn-left" variant="ghost"
                    :href="route('admin.kategorisasi.materi')" wire:navigate>
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
        <flux:callout.heading>{{ __('Tips Penyusunan Materi') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Pastikan kode materi unik dan konsisten antar subtes. Gunakan deskripsi untuk memandu penyusunan variasi soal.') }}
        </flux:callout.text>
    </flux:callout>
</div>
