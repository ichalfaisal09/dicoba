<?php

namespace App\Livewire\Admin\ManajemenTryout\Paket;

use App\Models\KonfigurasiDasarSistem;
use App\Models\TryoutPaket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Create extends Component
{
    public string $nama = '';

    public array $selectedKonfigurasi = [];

    public string $waktuPengerjaan = '';

    public string $harga = '0';

    public string $status = 'aktif';

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->roles()->where('nama', 'admin')->exists(),
            403
        );
    }

    public function resetForm(): void
    {
        $this->reset([
            'nama',
            'selectedKonfigurasi',
            'waktuPengerjaan',
            'harga',
            'status',
        ]);

        $this->status = 'aktif';

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store(): void
    {
        $validated = $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'selectedKonfigurasi' => ['required', 'array', 'min:1'],
            'selectedKonfigurasi.*' => [
                'integer',
                Rule::exists('konfigurasi_dasar_sistem', 'id'),
            ],
            'waktuPengerjaan' => ['required', 'integer', 'min:1'],
            'harga' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [], [
            'nama' => __('Nama Paket'),
            'selectedKonfigurasi' => __('Konfigurasi Dasar'),
            'selectedKonfigurasi.*' => __('Konfigurasi Dasar'),
            'waktuPengerjaan' => __('Waktu Pengerjaan'),
            'harga' => __('Harga'),
            'status' => __('Status'),
        ]);

        $konfigurasi = KonfigurasiDasarSistem::with('subtes')
            ->whereIn('id', $validated['selectedKonfigurasi'])
            ->orderBy('urutan')
            ->get();

        if ($konfigurasi->isEmpty()) {
            throw ValidationException::withMessages([
                'selectedKonfigurasi' => __('Setidaknya satu konfigurasi harus dipilih.'),
            ]);
        }

        $paket = TryoutPaket::create([
            'nama' => $validated['nama'],
            'waktu_pengerjaan' => $validated['waktuPengerjaan'],
            'harga' => $validated['harga'],
            'is_aktif' => $validated['status'],
        ]);

        $pivotData = [];

        foreach ($konfigurasi as $index => $item) {
            $pivotData[$item->id] = ['urutan' => $item->urutan ?? ($index + 1)];
        }

        $paket->konfigurasiDasar()->sync($pivotData);

        session()->flash('callout', [
            'icon' => 'check-circle',
            'variant' => 'success',
            'heading' => __('Paket tryout tersimpan'),
            'text' => __('Paket :nama berhasil dibuat dan siap dikelola.', ['nama' => $paket->nama]),
        ]);

        $this->redirectRoute('admin.manajemen-tryout.paket', navigate: true);
    }

    public function render()
    {
        $konfigurasiList = KonfigurasiDasarSistem::with('subtes')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get();

        return view('livewire.admin.manajementryout.paket.create', [
            'konfigurasiList' => $konfigurasiList,
        ])->layoutData([
            'title' => __('Tambah Paket Tryout'),
        ]);
    }
}
