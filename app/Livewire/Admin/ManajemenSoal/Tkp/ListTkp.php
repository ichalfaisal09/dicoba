<?php

namespace App\Livewire\Admin\ManajemenSoal\Tkp;

use App\Models\SoalPertanyaan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ListTkp extends Component
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

    public function render()
    {
        $soal = SoalPertanyaan::query()
            ->whereHas('variasi.materi.subtes', fn ($query) => $query->where('kode', 'TKP'))
            ->orderBy('kode_soal')
            ->paginate(10);

        return view('livewire.admin.manajemensoal.tkp.list', [
            'soal' => $soal,
        ])->layoutData([
            'title' => __('Manajemen Soal TKP'),
        ]);
    }

    public function confirmDeletion(int $id): void
    {
        session()->flash('callout', [
            'icon' => 'information-circle',
            'variant' => 'warning',
            'heading' => __('Fitur belum tersedia'),
            'text' => __('Hapus soal TKP akan tersedia setelah modul pengelolaan lanjutan selesai.'),
        ]);
    }
}
