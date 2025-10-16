<?php

namespace App\Livewire\Admin\Kategorisasi\Subtes;

use App\Models\KategoriSubtes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Create extends Component
{
    public string $entries = '';

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->roles()->where('nama', 'admin')->exists(),
            403
        );

        $this->entries = request()->string('entries')->value()
            ?? request()->string('form.entries')->value()
            ?? '';
    }

    public function resetForm(): void
    {
        $this->entries = '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate([
            'entries' => ['required', 'string'],
        ], [], [
            'entries' => __('Data Subtes'),
        ]);

        $rows = collect(preg_split("/\r\n|\n|\r/", trim($this->entries)))
            ->filter()
            ->map(function (string $line, int $index) {
                $segments = array_map('trim', explode(',', $line, 3));

                if (count($segments) < 3) {
                    throw ValidationException::withMessages([
                        'entries' => __('Format baris ke-:index tidak valid. Gunakan format kode,nama,deskripsi.', ['index' => $index + 1]),
                    ]);
                }

                [$kode, $nama, $deskripsi] = $segments;

                if ($kode === '' || $nama === '') {
                    throw ValidationException::withMessages([
                        'entries' => __('Kode dan nama wajib diisi pada baris ke-:index.', ['index' => $index + 1]),
                    ]);
                }

                return [
                    'kode' => Str::upper($kode),
                    'nama' => $nama,
                    'deskripsi' => $deskripsi !== '' ? $deskripsi : null,
                ];
            });

        foreach ($rows as $row) {
            KategoriSubtes::updateOrCreate(
                ['kode' => $row['kode']],
                [
                    'nama' => $row['nama'],
                    'deskripsi' => $row['deskripsi'],
                ]
            );
        }

        session()->flash('callout', [
            'icon' => 'check-circle',
            'variant' => 'success',
            'heading' => __('Subtes tersimpan'),
            'text' => __('Data subtes berhasil diperbarui.'),
        ]);

        return $this->redirectRoute('admin.kategorisasi.subtes', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.kategorisasi.subtes.create');
    }
}
