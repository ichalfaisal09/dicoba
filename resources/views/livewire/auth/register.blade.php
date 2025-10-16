<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Bikin akun baru')" :description="__('Masukin data kamu di bawah biar bisa lanjut')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Nama lengkap')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Masukin nama kamu')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email kamu')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Kata sandi')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Ulangi kata sandi')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Daftar sekarang') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Udah punya akun?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Masuk aja') }}</flux:link>
    </div>
</div>
