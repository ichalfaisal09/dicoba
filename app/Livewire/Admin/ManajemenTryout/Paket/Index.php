<?php

namespace App\Livewire\Admin\ManajemenTryout\Paket;

use App\Models\TryoutPaket;
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

    public function toggleStatus(int $paketId): void
    {
        $paket = TryoutPaket::findOrFail($paketId);

        $paket->update([
            'is_aktif' => $paket->is_aktif === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        session()->flash('callout', [
            'icon' => 'check-circle',
            'variant' => 'success',
            'heading' => __('Status paket diperbarui'),
            'text' => __('Status paket :nama kini :status.', [
                'nama' => $paket->nama,
                'status' => $paket->is_aktif === 'aktif' ? __('aktif') : __('nonaktif'),
            ]),
        ]);
    }

    public function render()
    {
        $paket = TryoutPaket::with(['konfigurasiDasar.subtes'])
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.admin.manajementryout.paket.list', [
            'paket' => $paket,
        ])->layoutData([
            'title' => __('Manajemen Tryout - Paket'),
        ]);
    }
}
