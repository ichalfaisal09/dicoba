<?php

namespace App\Livewire\Peserta\TryoutUjian;

use App\Models\SoalPertanyaan;
use App\Models\TryoutBooking;
use App\Models\TryoutSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Layout('layouts.app')]
class Index extends Component
{
    public TryoutBooking $booking;

    public function mount(int $bookingId): void
    {
        abort_unless(Auth::check(), 401);

        $booking = TryoutBooking::query()
            ->with('tryoutPaket')
            ->where('user_id', Auth::id())
            ->find($bookingId);

        if (! $booking) {
            throw new NotFoundHttpException();
        }

        $this->booking = $booking;
    }

    public function startExam(): void
    {
        if ($this->booking->status !== TryoutBooking::STATUS_ACTIVE) {
            session()->flash('callout', [
                'icon' => 'exclamation-circle',
                'variant' => 'danger',
                'heading' => __('Tryout belum dapat dimulai'),
                'text' => __('Status pendaftaran kamu belum aktif.'),
            ]);

            return;
        }

        $session = DB::transaction(function () {
            $existing = $this->booking->session()->lockForUpdate()->first();

            if (! $existing) {
                $existing = $this->booking->session()->create([
                    'status' => TryoutSession::STATUS_IN_PROGRESS,
                    'mulai_pada' => now(),
                    'metadata' => [
                        'initialized' => false,
                    ],
                ]);
            }

            $metadata = $existing->metadata ?? [];

            if (empty($metadata['question_ids'])) {
                $questionMetadata = $this->buildQuestionSet();

                $metadata = array_merge([
                    'initialized' => false,
                    'started_at' => null,
                    'remaining_seconds' => null,
                    'total_seconds' => null,
                    'last_synced_at' => null,
                ], $questionMetadata);
            }

            $metadata['initialized'] = $metadata['initialized'] ?? false;
            $metadata['current_index'] = $metadata['current_index'] ?? 0;
            $metadata['answers'] = $metadata['answers'] ?? [];
            $metadata['answered_count'] = $metadata['answered_count'] ?? 0;
            $metadata['total_questions'] = $metadata['total_questions'] ?? count($metadata['question_ids'] ?? []);
            $totalSeconds = $this->determineTotalSeconds();
            $metadata['remaining_seconds'] = $metadata['remaining_seconds'] ?? $totalSeconds;
            $metadata['total_seconds'] = $metadata['total_seconds'] ?? $totalSeconds;
            $metadata['started_at'] = $metadata['started_at'] ?? now()->toDateTimeString();
            $metadata['last_synced_at'] = $metadata['last_synced_at'] ?? now()->toDateTimeString();

            $existing->update([
                'status' => TryoutSession::STATUS_IN_PROGRESS,
                'mulai_pada' => $existing->mulai_pada ?? now(),
                'metadata' => $metadata,
            ]);

            return $existing->refresh();
        });

        $this->booking->setRelation('session', $session);

        $this->redirectRoute('peserta.tryout.ujian.halaman', ['bookingId' => $this->booking->id]);
    }

    public function render()
    {
        return view('livewire.peserta.tryoutujian.index')->layoutData([
            'title' => __('Tata Tertib Ujian Tryout'),
        ]);
    }

    protected function buildQuestionSet(): array
    {
        $questionIds = [];
        $sections = [];

        $konfigurasi = $this->booking
            ->tryoutPaket
            ->konfigurasiDasar()
            ->with('subtes')
            ->orderBy('konfigurasi_ke_tryout.urutan')
            ->get();

        foreach ($konfigurasi as $config) {
            $ids = SoalPertanyaan::query()
                ->where('status', 'aktif')
                ->whereHas('variasi', function ($query) use ($config) {
                    $query->whereHas('subtes', function ($subtes) use ($config) {
                        $subtes->where('kategori_subtes.id', $config->subtes_id);
                    });
                })
                ->inRandomOrder()
                ->limit($config->jumlah_soal)
                ->pluck('id')
                ->toArray();

            $questionIds = array_merge($questionIds, $ids);

            $sections[] = [
                'konfigurasi_id' => $config->id,
                'subtes_id' => $config->subtes_id,
                'question_ids' => $ids,
            ];
        }

        $questionIds = array_values($questionIds);

        return [
            'question_ids' => $questionIds,
            'sections' => $sections,
            'total_questions' => count($questionIds),
            'current_index' => 0,
            'answers' => [],
            'answered_count' => 0,
            'flags' => [],
            'question_cache' => [],
        ];
    }

    protected function determineTotalSeconds(): int
    {
        $minutes = $this->booking->tryoutPaket->waktu_pengerjaan ?? 0;

        if ($minutes <= 0) {
            return 0;
        }

        return (int) $minutes * 60;
    }
}
