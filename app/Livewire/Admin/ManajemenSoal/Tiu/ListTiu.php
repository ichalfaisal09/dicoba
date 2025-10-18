<?php

namespace App\Livewire\Admin\ManajemenSoal\Tiu;

use App\Models\SoalPertanyaan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ListTiu extends Component
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
            ->whereHas('variasi.materi.subtes', fn ($query) => $query->where('kode', 'TIU'))
            ->orderBy('kode_soal')
            ->paginate(10);

        return view('livewire.admin.manajemensoal.tiu.list', [
            'soal' => $soal,
        ])->layoutData([
            'title' => __('Manajemen Soal TIU'),
        ]);
    }

    public function confirmDeletion(int $id): void
    {
        session()->flash('callout', [
            'icon' => 'information-circle',
            'variant' => 'warning',
            'heading' => __('Fitur belum tersedia'),
            'text' => __('Hapus soal TIU akan tersedia setelah modul pengelolaan lanjutan selesai.'),
        ]);
    }
}
