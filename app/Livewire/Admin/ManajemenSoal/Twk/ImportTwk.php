<?php

namespace App\Livewire\Admin\ManajemenSoal\Twk;

use App\Models\KategoriVariasi;
use App\Models\SoalOpsiJawaban;
use App\Models\SoalPembahasan;
use App\Models\SoalPertanyaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class ImportTwk extends Component
{
    use WithFileUploads;

    public ?int $variasiId = null;

    public $jsonFile = null;

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->roles()->where('nama', 'admin')->exists(),
            403
        );
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

        return view('livewire.admin.manajemensoal.twk.import', [
            'variasiList' => $groupedVariasi,
        ])->layoutData([
            'title' => __('Import Soal TWK'),
        ]);
    }

    public function import(): void
    {
        $this->validate([
            'variasiId' => ['required', 'integer', Rule::exists('kategori_variasi', 'id')],
            'jsonFile' => ['required', 'file', 'mimetypes:application/json,text/plain', 'max:2048'],
        ], [], [
            'variasiId' => __('variasi soal'),
            'jsonFile' => __('berkas JSON'),
        ]);

        $rawContent = $this->jsonFile->get();
        $decoded = json_decode($rawContent, true);

        if (!is_array($decoded)) {
            throw ValidationException::withMessages([
                'jsonFile' => [__(' Struktur JSON tidak valid atau kosong. ')],
            ]);
        }

        if (empty($decoded)) {
            throw ValidationException::withMessages([
                'jsonFile' => [__('Berkas JSON tidak berisi soal.')],
            ]);
        }

        $preparedItems = [];
        $errors = [];

        foreach ($decoded as $index => $item) {
            $position = $index + 1;

            if (!is_array($item)) {
                $errors[] = __('Item :pos bukan objek JSON yang valid.', ['pos' => $position]);
                continue;
            }

            $validator = Validator::make($item, [
                'teks_soal' => ['required', 'string'],
                'tingkat_kesulitan' => ['required', Rule::in(['mudah', 'sedang', 'sulit'])],
                'opsi' => ['required', 'array', 'size:5'],
                'opsi.*.teks' => ['required', 'string'],
                'opsi.*.skor' => ['required', 'integer', 'between:0,5'],
                'opsi.*.huruf' => ['nullable', 'string', 'size:1'],
                'pembahasan' => ['nullable', 'string'],
                'referensi' => ['nullable', 'string', 'max:255'],
            ], [], [
                'teks_soal' => __('teks soal'),
                'tingkat_kesulitan' => __('tingkat kesulitan'),
                'opsi' => __('opsi jawaban'),
                'opsi.*.teks' => __('teks opsi'),
                'opsi.*.skor' => __('skor opsi'),
                'opsi.*.huruf' => __('huruf opsi'),
                'pembahasan' => __('pembahasan'),
                'referensi' => __('referensi'),
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = __('Item :pos: :message', [
                        'pos' => $position,
                        'message' => $message,
                    ]);
                }
                continue;
            }

            $opsiCollection = collect($item['opsi'])->map(function ($opsi, $opsiIndex) {
                $huruf = $opsi['huruf'] ?? chr(65 + $opsiIndex);

                return [
                    'huruf' => strtoupper(substr($huruf, 0, 1)),
                    'teks' => trim((string) $opsi['teks']),
                    'skor' => (int) $opsi['skor'],
                ];
            });

            if ($opsiCollection->firstWhere('skor', '>', 0) === null) {
                $errors[] = __('Item :pos: minimal satu opsi harus memiliki skor lebih dari 0.', ['pos' => $position]);
                continue;
            }

            $preparedItems[] = [
                'teks_soal' => trim((string) $item['teks_soal']),
                'tingkat_kesulitan' => $item['tingkat_kesulitan'],
                'opsi' => $opsiCollection->values()->all(),
                'teks_pembahasan' => $item['pembahasan'] ?? null,
                'referensi' => $item['referensi'] ?? null,
            ];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages([
                'jsonFile' => $errors,
            ]);
        }

        DB::transaction(function () use ($preparedItems) {
            foreach ($preparedItems as $item) {
                $kode = $this->generateKode();

                $soal = SoalPertanyaan::create([
                    'variasi_id' => $this->variasiId,
                    'kode_soal' => $kode,
                    'teks_soal' => $item['teks_soal'],
                    'tingkat_kesulitan' => $item['tingkat_kesulitan'],
                    'status' => 'aktif',
                ]);

                foreach ($item['opsi'] as $opsi) {
                    SoalOpsiJawaban::create([
                        'soal_id' => $soal->id,
                        'huruf_opsi' => $opsi['huruf'],
                        'teks_opsi' => $opsi['teks'],
                        'skor_opsi' => $opsi['skor'],
                    ]);
                }

                if (filled($item['teks_pembahasan']) || filled($item['referensi'])) {
                    SoalPembahasan::create([
                        'soal_id' => $soal->id,
                        'teks_pembahasan' => $item['teks_pembahasan'] ?: null,
                        'referensi' => $item['referensi'] ?: null,
                    ]);
                }
            }
        });

        session()->flash('callout', [
            'icon' => 'check-circle',
            'variant' => 'success',
            'heading' => __('Import soal berhasil'),
            'text' => __('Sebanyak :jumlah soal TWK berhasil diimpor.', ['jumlah' => count($preparedItems)]),
        ]);

        $this->resetForm();

        $this->redirectRoute('admin.manajemen-soal.twk');
    }

    public function updatedJsonFile(): void
    {
        $this->resetErrorBag('jsonFile');
        $this->resetValidation('jsonFile');
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

    protected function resetForm(): void
    {
        $this->reset(['variasiId', 'jsonFile']);

        $this->resetValidation();
    }
}
