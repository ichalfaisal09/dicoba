<div class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:card>
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">{{ __('Informasi Dasar Soal') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Pilih variasi TIU sebagai konteks soal. Variasi dikelompokkan berdasarkan materi agar mudah dicari.') }}
            </flux:text>
        </div>

        <div class="mt-6 space-y-6">
            <flux:field>
                <flux:label for="variasi">{{ __('Variasi Soal') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Sesuaikan variasi TIU agar distribusi tingkat kognitif tetap terpantau.') }}
                </flux:text>
                <flux:select id="variasi" wire:model="variasiId" placeholder="{{ __('Pilih variasi soal') }}">
                    <option value="">{{ __('Pilih Variasi') }}</option>
                    @foreach ($variasiList as $materi => $items)
                        <optgroup label="{{ $materi }}">
                            @foreach ($items as $variasi)
                                <option value="{{ $variasi->id }}">
                                    {{ $variasi->nama }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </flux:select>
                <flux:error name="variasiId" />
            </flux:field>
        </div>
    </flux:card>

    <flux:card>
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">{{ __('Konten Soal') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Tulis soal TIU dengan fokus pada kemampuan numerik, verbal, atau figural sesuai variasi.') }}
            </flux:text>
        </div>

        <div class="mt-6 space-y-6">
            <flux:field>
                <flux:label for="teksSoal">{{ __('Teks Soal') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Gunakan bahasa yang jelas dan singkat agar peserta mudah memahami konteks pertanyaan.') }}
                </flux:text>
                <flux:textarea id="teksSoal" wire:model.defer="teksSoal" rows="7"
                    placeholder="{{ __('Tuliskan soal TIU di sini...') }}"></flux:textarea>
                <flux:error name="teksSoal" />
            </flux:field>

            <flux:field>
                <flux:label for="kesulitan">{{ __('Tingkat Kesulitan') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Pilih tingkat kesulitan untuk membantu penyusunan blueprint dan analisis hasil belajar.') }}
                </flux:text>
                <flux:select id="kesulitan" wire:model="tingkatKesulitan">
                    <option value="mudah">{{ __('Mudah') }}</option>
                    <option value="sedang">{{ __('Sedang') }}</option>
                    <option value="sulit">{{ __('Sulit') }}</option>
                </flux:select>
                <flux:error name="tingkatKesulitan" />
            </flux:field>
        </div>
    </flux:card>

    <flux:card>
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">{{ __('Opsi Jawaban') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Sediakan opsi A–E. Gunakan skor 0–5 untuk menandai jawaban benar maupun pengecoh.') }}
            </flux:text>
        </div>

        <div class="mt-6 space-y-4">
            @foreach ($opsi as $index => $opsiItem)
                <div wire:key="opsi-tiu-{{ $opsiItem['huruf'] }}"
                    class="flex flex-col gap-4 rounded-2xl border border-zinc-200 p-4 shadow-xs dark:border-white/10">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <flux:badge variant="solid" color="primary" size="lg">
                                {{ $opsiItem['huruf'] }}
                            </flux:badge>
                            <div class="flex flex-col">
                                <flux:heading size="sm">{{ __('Opsi :huruf', ['huruf' => $opsiItem['huruf']]) }}</flux:heading>
                                <flux:text variant="muted" size="sm">
                                    {{ __('Pastikan pilihan jawaban mendukung penilaian kemampuan kognitif.') }}
                                </flux:text>
                            </div>
                        </div>

                        <div class="w-32">
                            <flux:field>
                                <flux:label for="skor-{{ $opsiItem['huruf'] }}">{{ __('Skor') }}</flux:label>
                                <flux:input id="skor-{{ $opsiItem['huruf'] }}" type="number" min="0" max="5" step="1"
                                    wire:model.defer="opsi.{{ $index }}.skor" suffix="{{ __('poin') }}" />
                                <flux:error :name="'opsi.' . $index . '.skor'" />
                            </flux:field>
                        </div>
                    </div>

                    <flux:field>
                        <flux:label for="teks-{{ $opsiItem['huruf'] }}">{{ __('Teks Opsi') }}</flux:label>
                        <flux:textarea id="teks-{{ $opsiItem['huruf'] }}" rows="3" resize="vertical"
                            wire:model.defer="opsi.{{ $index }}.teks"
                            placeholder="{{ __('Masukkan teks opsi untuk huruf :huruf', ['huruf' => $opsiItem['huruf']]) }}"></flux:textarea>
                        <flux:error :name="'opsi.' . $index . '.teks'" />
                    </flux:field>
                </div>
            @endforeach
        </div>
    </flux:card>

    <flux:card>
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">{{ __('Pembahasan & Referensi') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Berikan penjelasan singkat atau referensi sumber belajar untuk memperkuat pemahaman.') }}
            </flux:text>
        </div>

        <div class="mt-6 space-y-6">
            <flux:field>
                <flux:label for="pembahasan">{{ __('Teks Pembahasan') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Jelaskan mengapa jawaban tertentu mendapatkan skor tertinggi (opsional).') }}
                </flux:text>
                <flux:textarea id="pembahasan" rows="6" resize="vertical" wire:model.defer="teksPembahasan"
                    placeholder="{{ __('Tuliskan pembahasan singkat...') }}"></flux:textarea>
                <flux:error name="teksPembahasan" />
            </flux:field>

            <flux:field>
                <flux:label for="referensi">{{ __('Referensi') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Cantumkan sumber atau catatan yang mendukung soal TIU ini (opsional).') }}
                </flux:text>
                <flux:input id="referensi" type="text" wire:model.defer="referensi"
                    placeholder="{{ __('Contoh: Buku TIU CPNS 2025, Bab 4') }}" />
                <flux:error name="referensi" />
            </flux:field>
        </div>
    </flux:card>

    <flux:card>
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">{{ __('Aksi Form') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Pastikan seluruh data terisi benar sebelum menyimpan soal TIU ke bank soal.') }}
            </flux:text>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <flux:button type="submit" icon="check" wire:click.prevent="store" wire:loading.attr="disabled">
                {{ __('Simpan Soal') }}
            </flux:button>
            <flux:button icon="arrow-uturn-left" variant="ghost" :href="route('admin.manajemen-soal.tiu')" wire:navigate>
                {{ __('Kembali ke Daftar') }}
            </flux:button>
            <flux:button icon="trash" variant="danger" wire:click="resetForm" wire:loading.attr="disabled">
                {{ __('Reset Form') }}
            </flux:button>
        </div>
    </flux:card>
</div>
