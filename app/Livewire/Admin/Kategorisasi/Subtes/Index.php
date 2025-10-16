<?php

namespace App\Livewire\Admin\Kategorisasi\Subtes;

use App\Models\KategoriSubtes;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
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

    public function create(): void
    {
        $this->redirectRoute('admin.kategorisasi.subtes.create');
    }

    public function edit(int $subtesId): void
    {
        session()->flash('callout', [
            'icon' => 'information-circle',
            'variant' => 'secondary',
            'heading' => __('Fitur belum tersedia'),
            'text' => __('Fitur ubah subtes akan tersedia di pembaruan berikutnya.'),
        ]);
    }

    public function confirmDeletion(int $subtesId): void
    {
        KategoriSubtes::findOrFail($subtesId)->delete();

        session()->flash('callout', [
            'icon' => 'check-circle',
            'variant' => 'success',
            'heading' => __('Subtes dihapus'),
            'text' => __('Data subtes berhasil dihapus.'),
        ]);

        $this->resetPage();
    }

    public function render()
    {
        $subtes = KategoriSubtes::withCount('materi')
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.admin.kategorisasi.subtes.list', [
            'subtes' => $subtes,
        ])->layoutData([
            'title' => __('Kategori - Subtes'),
        ]);
    }
}
