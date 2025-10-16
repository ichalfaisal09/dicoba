<?php

namespace App\Livewire\Admin\Kategorisasi\Materi;

use App\Models\KategoriMateri;
use App\Models\KategoriSubtes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Create extends Component
{
    public string $subtesId = '';
    public string $entries = '';

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->roles()->where('nama', 'admin')->exists(),
            403
        );
    }

    public function resetForm(): void
    {
        $this->reset(['subtesId', 'entries']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $validated = $this->validate([
            'subtesId' => ['required', Rule::exists('kategori_subtes', 'id')->whereNull('deleted_at')],
            'entries' => ['required', 'string'],
        ], [], [
            'subtesId' => __('Subtes'),
            'entries' => __('Nama Materi'),
        ]);

        $subtesId = (int) $validated['subtesId'];

        $subtes = KategoriSubtes::findOrFail($subtesId);

        $lines = collect(preg_split("/\r\n|\n|\r/", $this->entries))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values();

        if ($lines->isEmpty()) {
            throw ValidationException::withMessages([
                'entries' => __('Minimal satu nama materi harus diisi.'),
            ]);
        }

        $created = 0;

        foreach ($lines as $index => $name) {
            if ($name === '') {
                throw ValidationException::withMessages([
                    'entries' => __('Baris ke-:index tidak boleh kosong.', ['index' => $index + 1]),
                ]);
            }

            $kode = $this->generateUniqueCode($subtes->kode);

            KategoriMateri::create([
                'kode' => $kode,
                'nama' => $name,
                'subtes_id' => $subtes->id,
                'deskripsi' => sprintf('%s — %s', $kode, $name),
            ]);

            $created++;
        }

        session()->flash('callout', [
            'icon' => 'check-circle',
            'variant' => 'success',
            'heading' => __('Materi tersimpan'),
            'text' => trans_choice(':count materi berhasil ditambahkan.', $created, ['count' => $created]),
        ]);

        return $this->redirectRoute('admin.kategorisasi.materi', navigate: true);
    }

    public function render()
    {
        $subtesList = KategoriSubtes::orderBy('nama')->get(['id', 'kode', 'nama']);

        return view('livewire.admin.kategorisasi.materi.create', [
            'subtesList' => $subtesList,
        ])->layoutData([
            'title' => __('Tambah Materi'),
        ]);
    }

    private function generateUniqueCode(string $prefix): string
    {
        $prefix = Str::upper(trim($prefix)) ?: 'MAT';

        do {
            $suffix = $this->randomAlphaNumeric(3);
            $kode = sprintf('%s-%s', $prefix, $suffix);
        } while (KategoriMateri::withTrashed()->where('kode', $kode)->exists());

        return $kode;
    }

    private function randomAlphaNumeric(int $length = 3): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $result;
    }
}
