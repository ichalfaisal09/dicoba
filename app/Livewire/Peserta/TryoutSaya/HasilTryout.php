<?php

namespace App\Livewire\Peserta\TryoutSaya;

use App\Models\TryoutBooking;
use App\Models\TryoutSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Layout('layouts.app')]
class HasilTryout extends Component
{
    public TryoutBooking $booking;

    public array $detail = [];

    public function mount(int $bookingId): void
    {
        abort_unless(Auth::check(), 401);

        $booking = TryoutBooking::query()
            ->with(['tryoutPaket.konfigurasiDasar', 'session.answers'])
            ->where('user_id', Auth::id())
            ->find($bookingId);

        if (! $booking) {
            throw new NotFoundHttpException();
        }

        $this->booking = $booking;

        $session = $booking->session;
        $sessionMetadata = $session?->metadata ?? [];
        $bookingMetadata = $booking->metadata ?? [];
        $answers = $session?->answers ?? Collection::make();

        $skorTotal = $bookingMetadata['skor_total']
            ?? ($sessionMetadata['total_score'] ?? (float) $answers->sum('skor'));

        $summary = $sessionMetadata['summary'] ?? [];

        $jawabanBenar = $bookingMetadata['jawaban_benar']
            ?? ($summary['correct'] ?? $answers->filter(fn ($answer) => ($answer->skor ?? 0) > 0)->count());

        $jawabanSalah = $bookingMetadata['jawaban_salah']
            ?? ($summary['incorrect'] ?? $answers->filter(fn ($answer) => ($answer->skor ?? 0) <= 0 && ! is_null($answer->jawaban_opsi_id))->count());

        $totalQuestions = $sessionMetadata['total_questions'] ?? max($answers->count(), $bookingMetadata['total_questions'] ?? 0);
        $jawabanTerjawab = $answers->whereNotNull('jawaban_opsi_id')->count();
        $jawabanKosong = $bookingMetadata['jawaban_kosong']
            ?? ($summary['unanswered'] ?? max(0, $totalQuestions - $jawabanTerjawab));

        $passingGradeItems = $booking->tryoutPaket?->konfigurasiDasar ?? Collection::make();

        $passingGrade = $passingGradeItems
            ->filter(fn ($konfigurasi) => ! is_null($konfigurasi->nilai_minimal))
            ->sum('nilai_minimal');

        $passingGradeBySubtest = $passingGradeItems
            ->map(function ($konfigurasi) {
                return [
                    'nama' => $konfigurasi->nama,
                    'nilai_minimal' => $konfigurasi->nilai_minimal,
                ];
            })
            ->filter(fn ($item) => ! is_null($item['nilai_minimal']))
            ->values()
            ->all();

        $lulus = null;

        if (! is_null($passingGrade) && $passingGrade > 0 && ! is_null($skorTotal)) {
            $lulus = $skorTotal >= $passingGrade;
        }

        $this->detail = [
            'paket' => [
                'nama' => $booking->tryoutPaket?->nama,
                'deskripsi' => $booking->tryoutPaket?->deskripsi,
            ],
            'skor_total' => $skorTotal,
            'peringkat' => $bookingMetadata['peringkat'] ?? null,
            'jawaban_benar' => $jawabanBenar,
            'jawaban_salah' => $jawabanSalah,
            'jawaban_kosong' => $jawabanKosong,
            'total_soal' => $totalQuestions,
            'pembahasan' => $bookingMetadata['pembahasan'] ?? [],
            'passing_grade' => [
                'total' => $passingGradeItems->isNotEmpty() ? ($passingGrade > 0 ? $passingGrade : null) : null,
                'by_subtests' => $passingGradeBySubtest,
                'status' => $lulus,
            ],
        ];
    }

    public function render()
    {
        return view('livewire.peserta.tryoutsaya.hasil')->layoutData([
            'title' => __('Hasil Tryout :nama', ['nama' => $this->booking->tryoutPaket->nama ?? __('Tidak diketahui')]),
        ]);
    }
}
