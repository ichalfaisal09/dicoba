<?php

namespace App\Livewire\Admin\ManajemenSoal\Twk;

use App\Models\SoalPertanyaan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ListTwk extends Component
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
            ->orderBy('kode_soal')
            ->paginate(10);

        return view('livewire.admin.manajemensoal.twk.list', [
            'soal' => $soal,
        ])->layoutData([
            'title' => __('Manajemen Soal TWK'),
        ]);
    }
}
