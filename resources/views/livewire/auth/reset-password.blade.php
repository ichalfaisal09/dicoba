<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Reset kata sandi')" :description="__('Masukin kata sandi baru kamu di bawah ya')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="resetPassword" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email kamu')"
            type="email"
            required
            autocomplete="email"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Kata sandi baru')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Tulis kata sandi baru')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Ulangi kata sandi baru')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Tulis ulang kata sandi')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Simpan kata sandi baru') }}
            </flux:button>
        </div>
    </form>
</div>
