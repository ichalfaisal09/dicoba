<div class="space-y-8">
    <div class="space-y-2">
        <flux:heading size="2xl">{{ __('Tata Tertib Ujian Tryout') }}</flux:heading>
        <flux:text variant="muted">
            {{ __('Baca dan pahami aturan ujian sebelum memulai sesi. Klik tombol mulai di bagian bawah jika kamu sudah siap mengerjakan tryout.') }}
        </flux:text>
    </div>

    <flux:card class="space-y-4">
        <flux:heading size="lg">{{ $booking->tryoutPaket->nama ?? __('Paket Tryout') }}</flux:heading>
        <flux:text variant="muted">
            {{ $booking->tryoutPaket->deskripsi ?? __('Tidak ada deskripsi tambahan untuk paket tryout ini.') }}
        </flux:text>

        <div class="grid gap-4 md:grid-cols-2">
            <flux:card variant="subtle">
                <flux:heading size="sm">{{ __('Durasi Disarankan') }}</flux:heading>
                <p class="mt-1 text-xl font-semibold">
                    {{ $booking->tryoutPaket->waktu_pengerjaan ?? __('Fleksibel') }} {{ __('menit') }}
                </p>
            </flux:card>

            <flux:card variant="subtle">
                <flux:heading size="sm">{{ __('Status Pendaftaran') }}</flux:heading>
                <flux:badge class="mt-2" color="success">{{ __('Aktif') }}</flux:badge>
            </flux:card>
        </div>
    </flux:card>

    <flux:card class="space-y-4">
        <flux:heading size="lg">{{ __('Peraturan Ujian') }}</flux:heading>

        <ul class="space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
            <li class="flex items-start gap-2">
                <flux:icon name="information-circle" class="mt-1 size-4 text-indigo-500" />
                <span>{{ __('Kerjakan tryout dalam satu sesi tanpa berganti tab atau menutup browser untuk menghindari kehilangan progres.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon name="cursor-arrow-rays" class="mt-1 size-4 text-emerald-500" />
                <span>{{ __('Pastikan koneksi internet stabil. Setiap jawaban disimpan otomatis ketika kamu berpindah soal.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon name="document-check" class="mt-1 size-4 text-amber-500" />
                <span>{{ __('Periksa kembali jawaban sebelum menekan tombol selesai. Skor akhir dihitung berdasarkan jawaban terakhir yang tersimpan.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon name="shield-check" class="mt-1 size-4 text-rose-500" />
                <span>{{ __('Dilarang bekerja sama atau menggunakan bantuan luar yang melanggar integritas ujian.') }}</span>
            </li>
        </ul>

        <flux:callout variant="muted" icon="clock">
            <flux:callout.text>
                {{ __('Jika kamu keluar sebelum selesai, gunakan tombol "Lanjutkan Tryout" dari halaman Tryout Saya untuk melanjutkan dari posisi terakhir.') }}
            </flux:callout.text>
        </flux:callout>
    </flux:card>

    <div class="flex flex-wrap gap-3">
        <flux:button variant="ghost" icon="arrow-uturn-left" :href="route('peserta.tryout-saya')" wire:navigate>
            {{ __('Kembali ke Tryout Saya') }}
        </flux:button>
        <flux:button icon="play" variant="primary" wire:click="startExam" wire:loading.attr="disabled">
            {{ __('Saya Siap Memulai Ujian') }}
        </flux:button>
    </div>
</div>
