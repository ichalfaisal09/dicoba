<?php

namespace App\Livewire\Admin\Konfigurasi;

use App\Models\KonfigurasiDasarSistem;
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

    public function render()
    {
        $konfigurasi = KonfigurasiDasarSistem::with('subtes')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->paginate(10);

        return view('livewire.admin.konfigurasi.list', [
            'konfigurasi' => $konfigurasi,
        ])->layoutData([
            'title' => __('Konfigurasi Dasar Sistem'),
        ]);
    }
}
