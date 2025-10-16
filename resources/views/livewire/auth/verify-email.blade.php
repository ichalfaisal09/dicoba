<div class="mt-4 flex flex-col gap-6">
    <flux:text class="text-center">
        {{ __('Cek email kamu dan klik link verifikasi yang baru aja kami kirim, ya!') }}
    </flux:text>

    @if (session('status') == 'verification-link-sent')
        <flux:text class="text-center font-medium !dark:text-green-400 !text-green-600">
            {{ __('Link verifikasi baru udah kami kirim ke email yang kamu pakai pas daftar.') }}
        </flux:text>
    @endif

    <div class="flex flex-col items-center justify-between space-y-3">
        <flux:button wire:click="sendVerification" variant="primary" class="w-full">
            {{ __('Kirim ulang email verifikasi') }}
        </flux:button>

        <flux:link class="text-sm cursor-pointer" wire:click="logout">
            {{ __('Keluar dari akun') }}
        </flux:link>
    </div>
</div>
