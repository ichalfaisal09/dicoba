<?php

namespace App\Livewire\Admin\Kategorisasi\Materi;

use App\Models\KategoriMateri;
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

    public function edit(int $materiId): void
    {
        session()->flash('callout', [
            'icon' => 'information-circle',
            'variant' => 'secondary',
            'heading' => __('Fitur belum tersedia'),
            'text' => __('Fitur ubah materi akan tersedia di pembaruan berikutnya.'),
        ]);
    }

    public function confirmDeletion(int $materiId): void
    {
        KategoriMateri::findOrFail($materiId)->delete();

        session()->flash('callout', [
            'icon' => 'check-circle',
            'variant' => 'success',
            'heading' => __('Materi dihapus'),
            'text' => __('Data materi berhasil dihapus.'),
        ]);

        $this->resetPage();
    }

    public function render()
    {
        $materi = KategoriMateri::with(['subtes'])
            ->withCount('variasi')
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.admin.kategorisasi.materi.list', [
            'materi' => $materi,
        ])->layoutData([
            'title' => __('Kategori - Materi'),
        ]);
    }
}
