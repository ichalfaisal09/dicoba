<div class="flex h-full w-full flex-1 flex-col items-center justify-center rounded-2xl border border-dashed border-zinc-200 bg-white p-10 text-center dark:border-white/10 dark:bg-white/5">
    <flux:icon name="wrench-screwdriver" class="size-12 text-zinc-400" />
    <flux:heading size="lg" class="mt-4">{{ __('Import Soal TKP Sedang Disiapkan') }}</flux:heading>
    <flux:text variant="muted" class="mt-2 max-w-2xl">
        {{ __('Halaman import JSON untuk soal TKP belum tersedia. Fitur ini akan mendukung unggah massal format penilaian TKP setelah skema dan validasinya selesai dirancang.') }}
    </flux:text>
    <flux:button icon="arrow-uturn-left" variant="ghost" class="mt-6" :href="route('admin.manajemen-soal.tkp')" wire:navigate>
        {{ __('Kembali ke Daftar') }}
    </flux:button>
</div>
