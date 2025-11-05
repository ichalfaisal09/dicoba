<?php

namespace App\Livewire\Admin\Kategorisasi\Variasi;

use App\Models\KategoriMateri;
use App\Models\KategoriSubtes;
use App\Models\KategoriVariasi;
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
    public string $materiId = '';
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
        $this->reset(['subtesId', 'materiId', 'entries']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $validated = $this->validate([
            'subtesId' => ['required', Rule::exists('kategori_subtes', 'id')->whereNull('deleted_at')],
            'materiId' => ['required', Rule::exists('kategori_materi', 'id')->whereNull('deleted_at')],
            'entries' => ['required', 'string'],
        ], [], [
            'subtesId' => __('Subtes'),
            'materiId' => __('Materi'),
            'entries' => __('Nama Variasi'),
        ]);

        $subtesId = (int) $validated['subtesId'];
        $materiId = (int) $validated['materiId'];

        $materi = KategoriMateri::with('subtes')
            ->where('subtes_id', $subtesId)
            ->findOrFail($materiId);

        $lines = collect(preg_split("/\r\n|\n|\r/", $this->entries))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values();

        if ($lines->isEmpty()) {
            throw ValidationException::withMessages([
                'entries' => __('Minimal satu nama variasi harus diisi.'),
            ]);
        }

        $created = 0;

        foreach ($lines as $index => $name) {
            if ($name === '') {
                throw ValidationException::withMessages([
                    'entries' => __('Baris ke-:index tidak boleh kosong.', ['index' => $index + 1]),
                ]);
            }

            $kode = $this->generateUniqueCode($materi->kode);

            KategoriVariasi::create([
                'kode' => $kode,
                'nama' => $name,
                'materi_id' => $materi->id,
                'deskripsi' => sprintf('%s — %s', $kode, $name),
            ]);

            $created++;
        }

        session()->flash('callout', [
            'icon' => 'check-circle',
            'variant' => 'success',
            'heading' => __('Variasi tersimpan'),
            'text' => trans_choice(':count variasi berhasil ditambahkan.', $created, ['count' => $created]),
        ]);

        return $this->redirectRoute('admin.kategorisasi.variasi', navigate: true);
    }

    public function updatedSubtesId(): void
    {
        $this->materiId = '';
    }

    public function render()
    {
        $subtesList = KategoriSubtes::orderBy('nama')->get(['id', 'kode', 'nama']);

        $materiList = $this->subtesId !== ''
            ? KategoriMateri::query()
                ->where('subtes_id', (int) $this->subtesId)
                ->orderBy('nama')
                ->get(['id', 'kode', 'nama'])
            : collect();

        return view('livewire.admin.kategorisasi.variasi.create', [
            'subtesList' => $subtesList,
            'materiList' => $materiList,
        ])->layoutData([
            'title' => __('Tambah Variasi'),
        ]);
    }

    private function generateUniqueCode(string $prefix): string
    {
        $prefix = Str::upper(trim($prefix)) ?: 'VAR';

        $lastNumber = KategoriVariasi::withTrashed()
            ->where('kode', 'like', $prefix . '-%')
            ->pluck('kode')
            ->map(fn (string $kode) => (int) Str::afterLast($kode, '-'))
            ->max();

        $next = $lastNumber ? $lastNumber + 1 : 1;

        return sprintf('%s-%03d', $prefix, $next);
    }
}
