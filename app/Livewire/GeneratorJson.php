<?php

namespace App\Livewire;

use App\Models\KategoriVariasi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GeneratorJson extends Component
{
    public ?int $variasiId = null;

    public array $variasiList = [];

    public int $jumlahTemplate = 10;

    public string $hasilGenerate = '';

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
            ->whereHas('materi.subtes', fn($query) => $query->where('kode', 'TKP'))
            ->with('materi:id,nama')
            ->orderBy(
                KategoriVariasi::query()
                    ->select('nama')
                    ->from('kategori_materi')
                    ->whereColumn('kategori_materi.id', 'kategori_variasi.materi_id')
                    ->limit(1)
            )
            ->orderBy('nama')
            ->get(['id', 'nama', 'materi_id']);

        $this->variasiList = $variasi
            ->groupBy(fn($item) => $item->materi?->nama ?? __('Tanpa Materi'))
            ->toArray();

        return view('livewire.generator', [
            'variasiList' => $this->variasiList,
        ])->layoutData([
            'title' => __('Generator Contoh JSON'),
        ]);
    }

    public function generate(): void
    {
        if (empty($this->variasiId)) {
            $this->hasilGenerate = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            return;
        }

        $variasi = KategoriVariasi::with('materi.subtes')->find($this->variasiId);

        if (! $variasi) {
            $this->hasilGenerate = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            return;
        }

        $jumlah = max(1, (int) $this->jumlahTemplate);
        $items = [];

        for ($i = 1; $i <= $jumlah; $i++) {
            $items[] = [
                'teks_soal' => sprintf('Contoh soal %d untuk variasi %s', $i, $variasi->nama),
                'tingkat_kesulitan' => 'sedang',
                'opsi' => [
                    ['huruf' => 'A', 'teks' => 'Contoh opsi A', 'skor' => 5],
                    ['huruf' => 'B', 'teks' => 'Contoh opsi B', 'skor' => 3],
                    ['huruf' => 'C', 'teks' => 'Contoh opsi C', 'skor' => 1],
                    ['huruf' => 'D', 'teks' => 'Contoh opsi D', 'skor' => 0],
                    ['huruf' => 'E', 'teks' => 'Contoh opsi E', 'skor' => 4],
                ],
                'pembahasan' => 'Contoh pembahasan singkat.',
                'referensi' => sprintf('Variasi %s - %s', $variasi->nama, optional($variasi->materi)->nama),
            ];
        }

        $this->hasilGenerate = json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function download()
    {
        if (blank($this->hasilGenerate)) {
            $this->generate();
        }

        $variasi = KategoriVariasi::find($this->variasiId);

        $slug = $variasi?->nama
            ? str($variasi->nama)->slug('-')->toString()
            : 'contoh-json';

        $filename = sprintf('%s-%s.json', $slug, now()->format('Ymd_His'));

        return response()->streamDownload(function () {
            echo $this->hasilGenerate;
        }, $filename, [
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
    }
}
