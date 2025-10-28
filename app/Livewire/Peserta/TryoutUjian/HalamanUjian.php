<?php

namespace App\Livewire\Peserta\TryoutUjian;

use App\Models\SoalPertanyaan;
use App\Models\TryoutBooking;
use App\Models\TryoutSession;
use App\Models\TryoutSessionAnswer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Layout('layouts.app')]
class HalamanUjian extends Component
{
    public TryoutBooking $booking;

    public TryoutSession $session;

    public bool $showCountdown = false;

    public int $countdown = 5;

    public array $metadata = [];

    public array $question = [];

    public array $options = [];

    public ?int $selectedOptionId = null;

    public int $currentIndex = 0;

    public int $questionNumber = 1;

    public int $totalQuestions = 0;

    public int $answeredCount = 0;

    public bool $isFirst = true;

    public bool $isLast = false;

    public bool $currentFlagged = false;

    public bool $showQuestionGrid = false;

    public bool $confirmSubmit = false;

    public int $remainingSeconds = 0;

    public int $totalSeconds = 0;

    public string $formattedCountdown = '00:00:00';

    public bool $timerActive = true;

    public float $countdownPercentage = 0.0;

    public function mount(int $bookingId): void
    {
        abort_unless(Auth::check(), 401);

        $booking = TryoutBooking::query()
            ->with(['tryoutPaket', 'session'])
            ->where('user_id', Auth::id())
            ->find($bookingId);

        if (! $booking) {
            throw new NotFoundHttpException();
        }

        $this->booking = $booking;
        $this->session = $booking->session;

        if (! $this->session) {
            throw new NotFoundHttpException();
        }

        $this->metadata = $this->session->metadata ?? [];
        $this->currentIndex = $this->metadata['current_index'] ?? 0;
        $this->totalQuestions = count($this->metadata['question_ids'] ?? []);
        $this->answeredCount = $this->metadata['answered_count'] ?? 0;
        $this->remainingSeconds = (int) ($this->metadata['remaining_seconds'] ?? 0);
        $this->totalSeconds = (int) ($this->metadata['total_seconds'] ?? 0);
        $this->updateFormattedCountdown();

        if ($this->totalQuestions === 0) {
            session()->flash('callout', [
                'icon' => 'exclamation-circle',
                'variant' => 'danger',
                'heading' => __('Soal belum tersedia'),
                'text' => __('Tidak ditemukan soal untuk sesi tryout ini. Silakan hubungi admin.'),
            ]);

            $this->redirectRoute('peserta.tryout.detail', $this->booking->tryout_paket_id);

            return;
        }

        $initialized = $this->metadata['initialized'] ?? false;

        if (! $initialized) {
            $this->showCountdown = true;
            $this->timerActive = false;
        } else {
            $this->resumeTimerIfNeeded();
            $this->loadCurrentQuestion();
        }
    }

    public function finishCountdown(): void
    {
        if (! $this->showCountdown) {
            return;
        }

        $metadata = $this->metadata;
        $metadata['initialized'] = true;
        $metadata['started_at'] = $metadata['started_at'] ?? now()->toDateTimeString();
        $metadata['last_synced_at'] = now()->toDateTimeString();

        $this->persistMetadata($metadata);

        $this->showCountdown = false;
        $this->metadata = $metadata;
        $this->timerActive = true;
        $this->resumeTimerIfNeeded();

        $this->loadCurrentQuestion();
    }

    public function selectOption(int $optionId): void
    {
        if ($this->showCountdown || empty($this->question['id'])) {
            return;
        }

        $option = collect($this->options)->firstWhere('id', $optionId);

        if (! $option) {
            return;
        }

        $metadata = $this->metadata;
        $questionId = (int) $this->question['id'];

        $answers = $metadata['answers'] ?? [];
        $answers[$questionId] = $optionId;
        $metadata['answers'] = $answers;
        $metadata['answered_count'] = count(array_filter($answers, fn ($value) => ! is_null($value)));

        $skor = $option['skor'] ?? null;

        $this->persistMetadata($metadata, function () use ($questionId, $optionId, $skor) {
            $existing = $this->session->answers()->where('soal_id', $questionId)->first();

            $isFlagged = $existing?->is_flagged ?? ($this->metadata['flags'][$questionId] ?? false);

            $this->session->answers()->updateOrCreate(
                [
                    'soal_id' => $questionId,
                ],
                [
                    'jawaban_opsi_id' => $optionId,
                    'is_flagged' => $isFlagged,
                    'skor' => $skor,
                ]
            );
        });

        $this->metadata = $metadata;
        $this->answeredCount = $metadata['answered_count'];
        $this->selectedOptionId = $optionId;
    }

    public function toggleFlag(): void
    {
        if ($this->showCountdown || empty($this->question['id'])) {
            return;
        }

        $questionId = (int) $this->question['id'];

        $flags = $this->metadata['flags'] ?? [];
        $flags[$questionId] = ! ($flags[$questionId] ?? false);
        $this->metadata['flags'] = $flags;

        $this->currentFlagged = $flags[$questionId];

        $this->persistMetadata($this->metadata, function () use ($questionId) {
            $this->session->answers()->updateOrCreate(
                [
                    'soal_id' => $questionId,
                ],
                [
                    'is_flagged' => $this->metadata['flags'][$questionId],
                ]
            );
        });
    }

    public function submitExam(bool $force = false): void
    {
        if ($this->showCountdown && ! $force) {
            return;
        }

        $flags = collect($this->metadata['flags'] ?? [])->filter()->count();

        if ($flags > 0 && ! $this->confirmSubmit && ! $force) {
            $this->confirmSubmit = true;

            return;
        }

        $this->confirmSubmit = false;

        $totalSkor = (float) $this->session->answers()->sum('skor');

        $metadata = $this->metadata;
        $metadata['submitted_at'] = now()->toDateTimeString();
        $metadata['total_score'] = $totalSkor;
        $metadata['remaining_seconds'] = max(0, $this->remainingSeconds);
        $metadata['last_synced_at'] = now()->toDateTimeString();

        DB::transaction(function () use ($metadata) {
            $this->session->update([
                'metadata' => $metadata,
                'status' => TryoutSession::STATUS_SUBMITTED,
                'selesai_pada' => now(),
                'durasi_terpakai' => $this->calculateDurationTerpakai(),
            ]);

            $this->session->refresh();
        });

        $this->booking->refresh();

        $this->timerActive = false;

        $this->redirectRoute('peserta.tryout.hasil', ['bookingId' => $this->booking->id]);
    }

    protected function calculateDurationTerpakai(): ?int
    {
        $mulai = $this->session->mulai_pada;

        if (! $mulai) {
            return null;
        }

        return $mulai->diffInSeconds(now());
    }

    public function openQuestionGrid(): void
    {
        if ($this->showCountdown) {
            return;
        }

        $this->showQuestionGrid = true;
    }

    public function closeQuestionGrid(): void
    {
        $this->showQuestionGrid = false;
    }

    public function jumpToQuestion(int $index): void
    {
        if ($this->showCountdown) {
            return;
        }

        $this->currentIndex = $index;
        $this->showQuestionGrid = false;
        $this->updateCurrentIndex();
    }

    public function cancelConfirmSubmit(): void
    {
        $this->confirmSubmit = false;
    }

    public function nextQuestion(): void
    {
        if ($this->showCountdown || $this->isLast) {
            return;
        }

        $this->currentIndex++;
        $this->updateCurrentIndex();
    }

    public function previousQuestion(): void
    {
        if ($this->showCountdown || $this->isFirst) {
            return;
        }

        $this->currentIndex--;
        $this->updateCurrentIndex();
    }

    public function render()
    {
        return view('livewire.peserta.tryoutujian.halamanujian')->layoutData([
            'title' => __('Tryout - Halaman Ujian'),
        ]);
    }

    protected function updateCurrentIndex(): void
    {
        $metadata = $this->metadata;
        $metadata['current_index'] = $this->currentIndex;

        $this->persistMetadata($metadata);

        $this->metadata = $metadata;

        $this->loadCurrentQuestion();
    }

    protected function loadCurrentQuestion(): void
    {
        $questionIds = $this->metadata['question_ids'] ?? [];

        if (empty($questionIds)) {
            $this->question = [];
            $this->options = [];

            return;
        }

        $this->totalQuestions = count($questionIds);

        if ($this->currentIndex < 0) {
            $this->currentIndex = 0;
        }

        if ($this->currentIndex >= $this->totalQuestions) {
            $this->currentIndex = max(0, $this->totalQuestions - 1);
        }

        $questionId = $questionIds[$this->currentIndex] ?? null;

        if (! $questionId) {
            $this->question = [];
            $this->options = [];

            return;
        }

        $question = $this->retrieveQuestion($questionId);

        if (! $question) {
            $this->question = [];
            $this->options = [];

            return;
        }

        $this->question = $question['question'];
        $this->options = $question['options'];

        $answers = $this->metadata['answers'] ?? [];
        $this->selectedOptionId = $answers[$this->question['id']] ?? null;

        $flags = $this->metadata['flags'] ?? [];
        $this->currentFlagged = $flags[$this->question['id']] ?? false;

        $this->questionNumber = $this->currentIndex + 1;
        $this->isFirst = $this->currentIndex === 0;
        $this->isLast = $this->currentIndex === $this->totalQuestions - 1;
        $this->answeredCount = $this->metadata['answered_count'] ?? 0;
    }

    public function tickTimer(int $step = 1): void
    {
        if (! $this->timerActive || $this->showCountdown) {
            return;
        }

        $this->remainingSeconds = max(0, $this->remainingSeconds - $step);
        $this->updateFormattedCountdown();

        $metadata = $this->metadata;
        $metadata['remaining_seconds'] = $this->remainingSeconds;
        $metadata['last_synced_at'] = now()->toDateTimeString();

        $this->persistMetadata($metadata);

        $this->metadata = $metadata;

        if ($this->remainingSeconds <= 0) {
            $this->timerActive = false;
            $this->submitExam(true);
        }
    }

    public function resumeTimerIfNeeded(): void
    {
        if (! $this->timerActive || $this->remainingSeconds <= 0) {
            return;
        }

        $lastSynced = isset($this->metadata['last_synced_at'])
            ? Carbon::parse($this->metadata['last_synced_at'])
            : null;

        if ($lastSynced) {
            $diff = $lastSynced->diffInSeconds(now());

            if ($diff > 0) {
                $this->remainingSeconds = max(0, $this->remainingSeconds - $diff);
                $this->updateFormattedCountdown();

                $metadata = $this->metadata;
                $metadata['remaining_seconds'] = $this->remainingSeconds;
                $metadata['last_synced_at'] = now()->toDateTimeString();

                $this->persistMetadata($metadata);

                $this->metadata = $metadata;
            }
        }

        if ($this->remainingSeconds <= 0) {
            $this->timerActive = false;
            $this->submitExam(true);
        }
    }

    protected function updateFormattedCountdown(): void
    {
        $seconds = max(0, $this->remainingSeconds);
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        $this->formattedCountdown = sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        $this->timerActive = $seconds > 0;
        $this->countdownPercentage = $this->totalSeconds > 0
            ? round(($seconds / $this->totalSeconds) * 100, 2)
            : 0;
    }

    protected function retrieveQuestion(int $questionId): ?array
    {
        $cache = $this->metadata['question_cache'] ?? [];

        if (isset($cache[$questionId])) {
            return $cache[$questionId];
        }

        $question = SoalPertanyaan::query()
            ->with([
                'opsiJawaban' => fn ($query) => $query->orderBy('huruf_opsi'),
                'variasi.subtes',
            ])
            ->find($questionId);

        if (! $question) {
            return null;
        }

        $payload = [
            'question' => [
                'id' => $question->id,
                'kode' => $question->kode_soal,
                'teks' => $question->teks_soal,
                'subtes' => $question->variasi?->subtes?->nama,
            ],
            'options' => $question->opsiJawaban
                ->map(fn ($opsi) => [
                    'id' => $opsi->id,
                    'label' => $opsi->huruf_opsi,
                    'teks' => $opsi->teks_opsi,
                    'skor' => $opsi->skor_opsi,
                ])
                ->toArray(),
        ];

        $cache[$questionId] = $payload;
        $this->metadata['question_cache'] = $cache;

        $this->persistMetadata($this->metadata);

        return $payload;
    }

    protected function persistMetadata(array $metadata, ?callable $beforeUpdate = null): void
    {
        DB::transaction(function () use ($metadata, $beforeUpdate) {
            if ($beforeUpdate) {
                $beforeUpdate();
            }

            $this->session->update([
                'metadata' => $metadata,
            ]);

            $this->session->refresh();
        });
    }
}
