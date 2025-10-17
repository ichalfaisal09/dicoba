<?php

namespace App\Livewire\Admin\Konfigurasi;

use App\Models\KategoriSubtes;
use App\Models\KonfigurasiDasarSistem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Create extends Component
{
    public string $subtesId = '';
    public string $nama = '';
    public string $jumlahSoal = '';
    public string $urutan = '';
    public string $nilaiMinimal = '';

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->roles()->where('nama', 'admin')->exists(),
            403
        );
    }

    public function resetForm(): void
    {
        $this->reset(['subtesId', 'nama', 'jumlahSoal', 'urutan', 'nilaiMinimal']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $validated = $this->validate([
            'subtesId' => ['required', Rule::exists('kategori_subtes', 'id')->whereNull('deleted_at')],
            'nama' => ['required', 'string', 'max:255'],
            'jumlahSoal' => ['required', 'integer', 'min:1'],
            'urutan' => ['required', 'integer', 'min:1'],
            'nilaiMinimal' => ['required', 'integer', 'min:0'],
        ], [], [
            'subtesId' => __('Subtes'),
            'nama' => __('Nama Konfigurasi'),
            'jumlahSoal' => __('Jumlah Soal'),
            'urutan' => __('Urutan'),
            'nilaiMinimal' => __('Nilai Minimal'),
        ]);

        KonfigurasiDasarSistem::create([
            'subtes_id' => (int) $validated['subtesId'],
            'nama' => $validated['nama'],
            'jumlah_soal' => (int) $validated['jumlahSoal'],
            'urutan' => (int) $validated['urutan'],
            'nilai_minimal' => (int) $validated['nilaiMinimal'],
        ]);

        session()->flash('callout', [
            'icon' => 'check-circle',
            'variant' => 'success',
            'heading' => __('Konfigurasi tersimpan'),
            'text' => __('Konfigurasi dasar sistem berhasil dibuat.'),
        ]);

        return $this->redirectRoute('admin.konfigurasi', navigate: true);
    }

    public function render()
    {
        $subtesList = KategoriSubtes::orderBy('nama')->get(['id', 'kode', 'nama']);

        return view('livewire.admin.konfigurasi.create', [
            'subtesList' => $subtesList,
        ])->layoutData([
            'title' => __('Tambah Konfigurasi Dasar'),
        ]);
    }
}
