 <div class="flex flex-col gap-6">
    <x-auth-header :title="__('Lupa kata sandi')" :description="__('Masukin email kamu biar kami kirim link reset')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email kamu')"
            type="email"
            required
            autofocus
            placeholder="email@example.com"
        />

        <flux:button variant="primary" type="submit" class="w-full">{{ __('Kirim link reset kata sandi') }}</flux:button>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
        <span>{{ __('Atau balik ke') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('halaman masuk') }}</flux:link>
    </div>
</div>
