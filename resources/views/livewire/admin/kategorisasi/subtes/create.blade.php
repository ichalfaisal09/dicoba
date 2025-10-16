<div class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:card>
        <div class="flex flex-col gap-3">
            <flux:heading size="lg">{{ __('Subtes Baru') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Input nama dan deskripsi subtes. Anda dapat memasukkan beberapa entri sekaligus, pisahkan setiap baris untuk diproses.') }}
            </flux:text>
        </div>

        <form class="mt-6 space-y-6" wire:submit.prevent="store">
            <flux:field>
                <flux:label for="entries">{{ __('Data Subtes') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Gunakan satu baris per entri dengan format: kode,nama,deskripsi. Deskripsi boleh dikosongkan namun tetap sertakan koma.') }}
                </flux:text>
                <flux:textarea id="entries" wire:model.defer="entries" rows="10" resize="vertical"
                    placeholder="Contoh:
TWK,Tes Wawasan Kebangsaan,Berisi soal wawasan kebangsaan.
TIU,Tes Intelegensi Umum,Mengukur kemampuan logika dan numerik.
TKP,Tes Karakter Pribadi," />
                <flux:error name="entries" />
            </flux:field>

            <div class="flex flex-wrap items-center gap-3">
                <flux:button type="submit" icon="check" wire:loading.attr="disabled">
                    {{ __('Simpan Subtes') }}
                </flux:button>
                <flux:button type="button" icon="arrow-uturn-left" variant="ghost"
                    :href="route('admin.kategorisasi.subtes')" wire:navigate>
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
        <flux:callout.heading>{{ __('Tips Format Multi Entri') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Pastikan urutan nama dan deskripsi sejajar baris demi baris. Jika jumlah baris tidak sama, sistem dapat mengabaikan entri yang tidak lengkap.') }}
        </flux:callout.text>
    </flux:callout>
</div>
