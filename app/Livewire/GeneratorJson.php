<?php

namespace App\Livewire;

use App\Models\KategoriSubtes;
use App\Models\KategoriVariasi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GeneratorJson extends Component
{
    public ?int $subtesId = null;

    public ?int $variasiId = null;

    public array $subtesOptions = [];

    public array $variasiOptions = [];

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->roles()->where('nama', 'admin')->exists(),
            403
        );

        $this->loadSubtes();
    }

    public function updatedSubtesId($value): void
    {
        $this->variasiId = null;
        $this->loadVariasi((int) $value);
    }

    public function render()
    {
        return view('livewire.generator')->layoutData([
            'title' => __('Generator Contoh JSON'),
        ]);
    }

    protected function loadSubtes(): void
    {
        $this->subtesOptions = KategoriSubtes::query()
            ->orderBy('nama')
            ->get(['id', 'kode', 'nama'])
            ->map(fn ($subtes) => [
                'value' => $subtes->id,
                'label' => trim(sprintf('%s - %s', $subtes->kode, $subtes->nama)),
            ])
            ->toArray();
    }

    protected function loadVariasi(?int $subtesId): void
    {
        if (empty($subtesId)) {
            $this->variasiOptions = [];

            return;
        }

        $this->variasiOptions = KategoriVariasi::query()
            ->whereHas('materi', fn ($query) => $query->where('subtes_id', $subtesId))
            ->orderBy('nama')
            ->get(['id', 'nama'])
            ->map(fn ($variasi) => [
                'value' => $variasi->id,
                'label' => $variasi->nama,
            ])
            ->toArray();
    }
}
