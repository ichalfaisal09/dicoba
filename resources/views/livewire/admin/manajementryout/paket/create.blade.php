<div class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:card>
        <div class="flex flex-col gap-3">
            <flux:heading size="lg">{{ __('Tambah Paket Tryout') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Susun paket tryout baru dengan mengatur nama, subtes yang disertakan, durasi pengerjaan, serta harga dan status tayang.') }}
            </flux:text>
        </div>

        <form class="mt-6 space-y-6" wire:submit.prevent="store">
            <flux:field>
                <flux:label for="nama">{{ __('Nama Paket') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Gunakan nama yang mudah dikenali peserta, misalnya Tryout Seleksi CPNS Gelombang 1.') }}
                </flux:text>
                <flux:input id="nama" type="text" wire:model.defer="nama"
                    placeholder="{{ __('Contoh: Tryout SKD Premium 2025') }}" />
                <flux:error name="nama" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Konfigurasi Dasar (Subtes)') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Pilih satu atau lebih konfigurasi dasar untuk disusun sebagai paket. Setiap konfigurasi sudah mencakup subtes, jumlah soal, dan urutan default.') }}
                </flux:text>

                <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($konfigurasiList as $konfigurasi)
                        <label wire:key="konfigurasi-{{ $konfigurasi->id }}"
                            class="flex flex-col gap-2 rounded-xl border border-zinc-200 p-4 shadow-xs transition hover:border-zinc-300 dark:border-white/10 dark:hover:border-white/20">
                            <div class="flex items-start gap-3">
                                <flux:checkbox wire:model.defer="selectedKonfigurasi" value="{{ $konfigurasi->id }}" />
                                <div class="flex flex-col">
                                    <flux:heading size="sm">{{ $konfigurasi->nama }}</flux:heading>
                                    <flux:text variant="muted">
                                        {{ __('Subtes: :subtes', ['subtes' => $konfigurasi->subtes?->nama ?? __('Tidak diketahui')]) }}
                                    </flux:text>
                                    <flux:badge variant="soft" color="neutral">
                                        {{ __('Urutan :urutan • :soal soal', ['urutan' => $konfigurasi->urutan, 'soal' => $konfigurasi->jumlah_soal]) }}
                                    </flux:badge>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                <flux:error name="selectedKonfigurasi" />
            </flux:field>

            <div class="grid gap-4 md:grid-cols-3">
                <flux:field>
                    <flux:label for="waktu">{{ __('Waktu Pengerjaan') }}</flux:label>
                    <flux:text variant="muted">
                        {{ __('Masukkan durasi total dalam menit. Peserta akan melihat informasi ini sebelum mulai tryout.') }}
                    </flux:text>
                    <flux:input id="waktu" type="number" min="1" wire:model.defer="waktuPengerjaan"
                        placeholder="{{ __('Misal: 120') }}" suffix="{{ __('menit') }}" />
                    <flux:error name="waktuPengerjaan" />
                </flux:field>

                <flux:field>
                    <flux:label for="harga">{{ __('Harga Paket') }}</flux:label>
                    <flux:text variant="muted">
                        {{ __('Secara default bernilai 0 (gratis). Isi angka tanpa tanda pemisah.') }}
                    </flux:text>
                    <flux:input id="harga" type="number" min="0" wire:model.defer="harga"
                        placeholder="{{ __('0') }}" prefix="Rp" />
                    <flux:error name="harga" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Status Paket') }}</flux:label>
                    <flux:text variant="muted">
                        {{ __('Tentukan apakah paket langsung ditayangkan atau dinonaktifkan sementara.') }}
                    </flux:text>
                    <flux:radio.group variant="segmented" wire:model.defer="status">
                        <flux:radio value="aktif" label="Aktif" />
                        <flux:radio value="nonaktif" label="Nonaktif" />
                    </flux:radio.group>
                    <flux:error name="status" />
                </flux:field>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <flux:button type="submit" icon="check" wire:loading.attr="disabled">
                    {{ __('Simpan Paket') }}
                </flux:button>
                <flux:button type="button" icon="arrow-uturn-left" variant="ghost"
                    :href="route('admin.manajemen-tryout.paket')" wire:navigate>
                    {{ __('Kembali ke Daftar') }}
                </flux:button>
                <flux:button type="button" icon="trash" variant="danger" wire:click="resetForm"
                    wire:loading.attr="disabled">
                    {{ __('Reset Form') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
