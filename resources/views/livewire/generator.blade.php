<div class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:card>
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">{{ __('Generator Contoh JSON') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Pilih subtes dan variasi untuk menyiapkan template JSON yang sesuai.') }}
            </flux:text>
        </div>

        <div class="mt-6 space-y-6">
            <flux:field>
                <flux:label for="subtes">{{ __('Subtes') }}</flux:label>
                <flux:text variant="muted">
                    {{ __('Pilih subtes untuk menampilkan variasi terkait.') }}
                </flux:text>
                <flux:select id="subtes" wire:model="subtesId" placeholder="{{ __('Pilih subtes') }}">
                    <option value="">{{ __('Pilih Subtes') }}</option>
                    @foreach ($subtesOptions as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
        </div>
    </flux:card>
</div>
