<?php

namespace App\Livewire\Admin\Kategorisasi\Variasi;

use App\Models\KategoriVariasi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->roles()->where('nama', 'admin')->exists(),
            403
        );
    }

    public function refresh(): void
    {
        $this->resetPage();
    }

    public function edit(int $variasiId): void
    {
        session()->flash('callout', [
            'icon' => 'information-circle',
            'variant' => 'secondary',
            'heading' => __('Fitur belum tersedia'),
            'text' => __('Fitur ubah variasi akan tersedia di pembaruan berikutnya.'),
        ]);
    }

    public function confirmDeletion(int $variasiId): void
    {
        KategoriVariasi::findOrFail($variasiId)->delete();

        session()->flash('callout', [
            'icon' => 'check-circle',
            'variant' => 'success',
            'heading' => __('Variasi dihapus'),
            'text' => __('Data variasi berhasil dihapus.'),
        ]);

        $this->resetPage();
    }

    public function render()
    {
        $variasi = KategoriVariasi::with(['materi.subtes'])
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.admin.kategorisasi.variasi.list', [
            'variasi' => $variasi,
        ])->layoutData([
            'title' => __('Kategori - Variasi'),
        ]);
    }
}
