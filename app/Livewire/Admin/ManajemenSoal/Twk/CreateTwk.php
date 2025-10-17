<?php

namespace App\Livewire\Admin\ManajemenSoal\Twk;

use App\Models\KategoriVariasi;
use App\Models\SoalOpsiJawaban;
use App\Models\SoalPembahasan;
use App\Models\SoalPertanyaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CreateTwk extends Component
{
    public ?int $variasiId = null;

    public string $kodeSoal = '';

    public string $teksSoal = '';

    public string $tingkatKesulitan = 'sedang';

    public array $opsi = [];

    public string $teksPembahasan = '';

    public string $referensi = '';

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->roles()->where('nama', 'admin')->exists(),
            403
        );
        $this->kodeSoal = $this->generateKode();

        $this->opsi = collect(range('A', 'E'))
            ->map(fn ($huruf) => [
                'huruf' => $huruf,
                'teks' => '',
                'skor' => 0,
            ])
            ->values()
            ->toArray();
    }

    public function render()
    {
        $variasi = KategoriVariasi::query()
            ->whereHas('materi.subtes', fn ($query) => $query->where('kode', 'TWK'))
            ->with('materi:id,nama')
            ->orderBy(
                KategoriVariasi::query()
                    ->select('nama')
                    ->from('kategori_materi')
                    ->whereColumn('kategori_materi.id', 'kategori_variasi.materi_id')
                    ->limit(1)
            )
            ->orderBy('nama')
            ->get(['id', 'kode', 'nama', 'materi_id']);

        $groupedVariasi = $variasi->groupBy(fn ($item) => $item->materi?->nama ?? __('Tanpa Materi'));

        return view('livewire.admin.manajemensoal.twk.create', [
            'variasiList' => $groupedVariasi,
        ])
            ->layoutData([
                'title' => __('Tambah Soal TWK'),
            ]);
    }

    protected function generateKode(): string
    {
        $latest = SoalPertanyaan::where('kode_soal', 'like', 'TWK-%')
            ->latest('kode_soal')
            ->value('kode_soal');

        $number = 1;

        if ($latest) {
            $number = (int) substr($latest, 4) + 1;
        }

        return sprintf('TWK-%03d', $number);
    }

    protected function rules(): array
    {
        return [
            'variasiId' => ['required', 'integer', Rule::exists('kategori_variasi', 'id')],
            'teksSoal' => ['required', 'string'],
            'tingkatKesulitan' => ['required', Rule::in(['mudah', 'sedang', 'sulit'])],
                        'opsi' => ['required', 'array', 'size:5'],
            'opsi.*.teks' => ['required', 'string'],
            'opsi.*.skor' => ['required', 'integer', 'between:0,5'],
            'teksPembahasan' => ['nullable', 'string'],
            'referensi' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'variasiId' => __('variasi soal'),
            'teksSoal' => __('teks soal'),
            'tingkatKesulitan' => __('tingkat kesulitan'),
            'opsi.*.teks' => __('teks opsi'),
            'opsi.*.skor' => __('skor opsi'),
            'teksPembahasan' => __('teks pembahasan'),
            'referensi' => __('referensi'),
        ];
    }

    public function store(): void
    {
        $this->validate($this->rules(), [], $this->validationAttributes());

        $opsiCollection = collect($this->opsi)
            ->map(fn ($opsi) => [
                'huruf' => $opsi['huruf'],
                'teks' => trim((string) $opsi['teks']),
                'skor' => (int) $opsi['skor'],
            ]);

        if ($opsiCollection->firstWhere('skor', '>', 0) === null) {
            throw ValidationException::withMessages([
                'opsi' => [__('Minimal satu opsi harus memiliki skor lebih dari 0.')],
            ]);
        }

        $this->kodeSoal = $this->generateKode();

        DB::transaction(function () use ($opsiCollection) {
            $soal = SoalPertanyaan::create([
                'variasi_id' => $this->variasiId,
                'kode_soal' => $this->kodeSoal,
                'teks_soal' => $this->teksSoal,
                'tingkat_kesulitan' => $this->tingkatKesulitan,
                'status' => 'aktif',
            ]);

            $opsiCollection->each(function ($opsi) use ($soal) {
                SoalOpsiJawaban::create([
                    'soal_id' => $soal->id,
                    'huruf_opsi' => $opsi['huruf'],
                    'teks_opsi' => $opsi['teks'],
                    'skor_opsi' => $opsi['skor'],
                ]);
            });

            if (filled($this->teksPembahasan) || filled($this->referensi)) {
                SoalPembahasan::create([
                    'soal_id' => $soal->id,
                    'teks_pembahasan' => $this->teksPembahasan ?: null,
                    'referensi' => $this->referensi ?: null,
                ]);
            }
        });

        session()->flash('callout', [
            'icon' => 'check-circle',
            'variant' => 'success',
            'heading' => __('Soal berhasil disimpan'),
            'text' => __('Soal TWK baru telah ditambahkan ke bank soal.'),
        ]);

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset(['variasiId', 'teksSoal', 'tingkatKesulitan', 'opsi', 'teksPembahasan', 'referensi']);

        $this->tingkatKesulitan = 'sedang';
        $this->opsi = collect(range('A', 'E'))
            ->map(fn ($huruf) => [
                'huruf' => $huruf,
                'teks' => '',
                'skor' => 0,
            ])
            ->values()
            ->toArray();
        $this->teksPembahasan = '';
        $this->referensi = '';
        $this->kodeSoal = $this->generateKode();

        $this->resetValidation();
    }
}
