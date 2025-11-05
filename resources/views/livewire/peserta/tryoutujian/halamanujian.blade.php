<div class="space-y-6 cursor-pointer">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">
                {{ $booking->tryoutPaket->nama ?? __('Tryout') }}
            </flux:heading>
            <flux:text variant="muted">
                {{ __('Sesi ujian untuk :nama', ['nama' => $booking->user->name ?? __('Peserta')]) }}
            </flux:text>
        </div>

        <div class="text-right">
            <flux:text size="sm" class="text-zinc-500">{{ __('Status Sesi') }}</flux:text>
            <flux:badge color="success">{{ __('Sedang berlangsung') }}</flux:badge>

            @if ($totalSeconds > 0)
                <div class="mt-3 space-y-2">
                    <flux:text size="sm" class="text-zinc-500">{{ __('Sisa Waktu') }}</flux:text>
                    <div class="flex items-end justify-end gap-2">
                        <span class="text-2xl font-semibold text-indigo-600">
                            {{ $formattedCountdown }}
                        </span>
                    </div>
                    <div class="h-2 w-48 rounded-full bg-zinc-200">
                        <div class="h-2 rounded-full bg-indigo-500 transition-all"
                            style="width: {{ max(0, min(100, $countdownPercentage)) }}%;"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if ($showCountdown)
        <flux:card class="space-y-4" x-data="{
            countdown: {{ $countdown }},
            init() {
                this.tick();
            },
            tick() {
                if (this.countdown > 0) {
                    setTimeout(() => {
                        this.countdown--;
                        this.tick();
                    }, 1000);
                } else {
                    $wire.finishCountdown();
                }
            }
        }">
            <flux:heading size="lg">{{ __('Menyiapkan sesi ujian') }}</flux:heading>
            <flux:text variant="muted">
                {{ __('Mohon tunggu, kami sedang menyiapkan daftar soal untukmu. Ujian akan dimulai otomatis dalam hitungan mundur di bawah ini.') }}
            </flux:text>

            <div class="flex items-center justify-center">
                <div
                    class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-indigo-500 text-4xl font-bold text-indigo-500">
                    <span x-text="countdown"></span>
                </div>
            </div>
        </flux:card>
    @else
        <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
            <flux:card class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">
                            {{ __('Soal Nomor :nomor', ['nomor' => $questionNumber]) }}
                        </flux:heading>
                        @if ($question['subtes'] ?? false)
                            <flux:text size="sm" variant="muted">
                                {{ __('Subtes: :subtes', ['subtes' => $question['subtes']]) }}
                            </flux:text>
                        @endif
                    </div>
                    @if ($totalSeconds > 0)
                        <flux:badge color="indigo">{{ __('Timer aktif') }}</flux:badge>
                    @else
                        <flux:badge>{{ __('Durasi fleksibel') }}</flux:badge>
                    @endif
                </div>

                <div class="prose dark:prose-invert">
                    {!! $question['teks'] ?? __('Soal tidak ditemukan.') !!}
                </div>

                <div class="space-y-3">
                    <flux:heading size="sm">{{ __('Pilihan Jawaban') }}</flux:heading>
                    <div class="grid gap-3">
                        @forelse ($options as $option)
                            <flux:button class="justify-start"
                                :variant="$selectedOptionId === $option['id'] ? 'primary' : 'outline'"
                                wire:key="option-{{ $option['id'] }}" wire:click="selectOption({{ $option['id'] }})"
                                wire:loading.attr="disabled">
                                <span class="mr-3 font-semibold">{{ $option['label'] }}</span>
                                <span class="text-left">{!! $option['teks'] !!}</span>
                            </flux:button>
                        @empty
                            <flux:callout variant="warning" icon="exclamation-triangle">
                                <flux:callout.text>{{ __('Belum ada opsi jawaban untuk soal ini.') }}
                                </flux:callout.text>
                            </flux:callout>
                        @endforelse
                    </div>
                </div>

                <div class="flex flex-wrap justify-between gap-3">
                    <flux:button variant="outline" icon="arrow-left" wire:click="previousQuestion"
                        :disabled="$isFirst" wire:loading.attr="disabled">
                        {{ __('Sebelumnya') }}
                    </flux:button>
                    <div class="flex gap-3">
                        <flux:button variant="outline" :color="$currentFlagged ? 'warning' : 'neutral'"
                            :icon="$currentFlagged ? 'exclamation-triangle' : 'flag'" wire:click="toggleFlag"
                            wire:loading.attr="disabled">
                            {{ $currentFlagged ? __('Batalkan Ragu') : __('Tandai Ragu') }}
                        </flux:button>
                        <flux:button icon="arrow-right" wire:click="nextQuestion" :disabled="$isLast"
                            wire:loading.attr="disabled">
                            {{ __('Selanjutnya') }}
                        </flux:button>
                    </div>
                </div>
            </flux:card>

            <div class="space-y-4">
                <flux:card class="space-y-3">
                    <flux:heading size="md">{{ __('Ringkasan Sesi') }}</flux:heading>
                    @php
                        $questionIds = $metadata['question_ids'] ?? [];
                        $answers = $metadata['answers'] ?? [];
                        $flags = $metadata['flags'] ?? [];
                        $flaggedTotal = collect($flags)->filter()->count();
                    @endphp
                    <div class="space-y-2">
                        <p class="text-sm text-zinc-500">{{ __('Total Soal') }}:
                            <strong>{{ $totalQuestions }}</strong>
                        </p>
                        <p class="text-sm text-zinc-500">{{ __('Terjawab') }}: <strong>{{ $answeredCount }}</strong>
                        </p>
                        <p class="text-sm text-zinc-500">{{ __('Ditandai Ragu') }}:
                            <strong>{{ $flaggedTotal }}</strong>
                        </p>
                    </div>
                    <flux:button variant="outline" icon="squares-2x2" class="flex items-center gap-2"
                        wire:click="openQuestionGrid" wire:loading.attr="disabled">
                        {{ __('Lihat Daftar Soal') }}
                    </flux:button>
                    <flux:button color="success" icon="check-circle" wire:click="submitExam"
                        wire:loading.attr="disabled">
                        {{ __('Selesai & Kirim Jawaban') }}
                    </flux:button>
                </flux:card>

                @if ($confirmSubmit)
                    <flux:card variant="warning" class="space-y-4">
                        <flux:heading size="md">{{ __('Konfirmasi Pengiriman Jawaban') }}</flux:heading>
                        <div class="space-y-2 text-sm text-zinc-700">
                            <p>{{ __('Pastikan kamu sudah memeriksa seluruh jawaban sebelum dikirim.') }}</p>
                            @if ($confirmUnansweredCount > 0)
                                <p>
                                    {{ trans_choice(':count soal belum dijawab.', $confirmUnansweredCount, ['count' => $confirmUnansweredCount]) }}
                                </p>
                            @endif
                            @if ($confirmFlaggedCount > 0)
                                <p>
                                    {{ trans_choice(':count soal masih ditandai ragu.', $confirmFlaggedCount, ['count' => $confirmFlaggedCount]) }}
                                </p>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <flux:button variant="outline" wire:click="cancelConfirmSubmit" wire:loading.attr="disabled">
                                {{ __('Periksa Lagi') }}
                            </flux:button>
                            <flux:button color="danger" icon="check" wire:click="submitExam" wire:loading.attr="disabled">
                                {{ __('Kirim Sekarang') }}
                            </flux:button>
                        </div>
                    </flux:card>
                @endif

                {{-- <flux:card class="space-y-3">
                    <flux:heading size="md">{{ __('Catatan') }}</flux:heading>
                    <flux:text variant="muted">
                        {{ __('Isi konten soal, navigasi, dan logika penyimpanan sesuai kebutuhan. Struktur ini disiapkan sebagai kerangka awal.') }}
                    </flux:text>
                </flux:card>
                <flux:card class="space-y-3">
                    <flux:heading size="md">{{ __('Countdown Timer') }}</flux:heading>
                    <div class="flex items-center justify-center">
                        <div
                            class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-indigo-500 text-4xl font-bold text-indigo-500">
                            <span>{{ $formattedCountdown }}</span>
                        </div>
                    </div>
                    <div class="h-2 w-48 rounded-full bg-zinc-200">
                        <div
                            class="h-2 rounded-full bg-indigo-500 transition-all"
                            style="width: {{ max(0, min(100, $countdownPercentage)) }}%;"
                        ></div>
                    </div>
                </flux:card> --}}
            </div>
        </div>
    @endif

    @if ($showQuestionGrid)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <flux:card class="w-full max-w-3xl space-y-4" x-data="{
                current: @entangle('currentIndex').live,
                total: {{ count($questionIds) }},
                move(delta) {
                    let next = this.current + delta;
                    if (next < 0) {
                        next = 0;
                    }
                    if (next >= this.total) {
                        next = this.total - 1;
                    }
                    this.current = next;
                    $wire.jumpToQuestion(next);
                },
                handleKey(event) {
                    switch (event.key) {
                        case 'ArrowRight':
                            event.preventDefault();
                            this.move(1);
                            break;
                        case 'ArrowLeft':
                            event.preventDefault();
                            this.move(-1);
                            break;
                        case 'ArrowDown':
                            event.preventDefault();
                            this.move(5);
                            break;
                        case 'ArrowUp':
                            event.preventDefault();
                            this.move(-5);
                            break;
                        case 'Enter':
                            event.preventDefault();
                            $wire.jumpToQuestion(this.current);
                            $wire.closeQuestionGrid();
                            break;
                        case 'Escape':
                            event.preventDefault();
                            $wire.closeQuestionGrid();
                            break;
                    }
                }
            }"
                x-on:keydown.window="handleKey($event)">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">{{ __('Daftar Soal') }}</flux:heading>
                    <flux:button variant="ghost" icon="x-mark" wire:click="closeQuestionGrid" />
                </div>
                <div class="grid grid-cols-5 gap-3 sm:grid-cols-8 lg:grid-cols-10">
                    @foreach ($questionIds as $index => $id)
                        @php
                            $isCurrent = $index === $currentIndex;
                            $isAnswered = isset($answers[$id]);
                            $isFlagged = $flags[$id] ?? false;

                            $buttonStyle = match (true) {
                                $isCurrent => 'background-color: #4f46e5; border-color: #4f46e5; color: #fff;',
                                $isFlagged => 'background-color: #fef3c7; border-color: #fbbf24; color: #78350f;',
                                $isAnswered => 'background-color: #dcfce7; border-color: #34d399; color: #064e3b;',
                                default => 'background-color: #fff; border-color: #e5e7eb; color: #3f3f46;',
                            };
                        @endphp
                        <button type="button"
                            class="flex h-10 items-center justify-center rounded-lg border text-sm font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                            style="{{ $buttonStyle }}" wire:key="grid-question-{{ $id }}"
                            wire:click="jumpToQuestion({{ $index }})" wire:loading.attr="disabled">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
                <flux:text variant="muted">
                    {{ __('Hijau: sudah dijawab · Kuning: ditandai ragu · Biru: posisi saat ini') }}
                </flux:text>
            </flux:card>
        </div>
    @endif

    @if ($timerActive && $totalSeconds > 0)
        <span wire:poll.keep-alive.1s="tickTimer" class="hidden"></span>
    @endif
</div>
