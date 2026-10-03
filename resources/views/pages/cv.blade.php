@php
    use App\Enums\ExperienceKind;
    $name     = public_text($siteSetting?->name) ?? config('app.name', 'Portofolio');
    $role     = public_text($siteSetting?->role);
    $location = $siteSetting?->location;

    // Ringkasan: pakai cv_summary jika ada, fallback ke intro_home
    $rawSummary = $siteSetting?->cv_summary ?? null;
    $summary    = public_text($rawSummary) ?? public_text($siteSetting?->intro_home);

    // Foto: hanya bila dua syarat terpenuhi
    $showPhoto = ($siteSetting?->cv_show_photo ?? false) && !empty($siteSetting?->photo);

    // Keahlian
    $skills = $siteSetting?->skills ?? [];

    // Section helpers – dapatkan group experiences
    $workExps = $experiences->get(ExperienceKind::Kerja->value, collect());
    $internExps = $experiences->get(ExperienceKind::Magang->value, collect());
    $orgExps = $experiences->get(ExperienceKind::Organisasi->value, collect());
    $eduExps = $experiences->get(ExperienceKind::Pendidikan->value, collect());
@endphp
<x-layouts.cv :title="$name . ' — CV'">

{{-- ═══ KERTAS A4 ═══ --}}
<article class="cv-content p-8 md:p-10 print:p-0" aria-label="Curriculum Vitae">

    {{-- ─── KEPALA ─────────────────────────────────────────────────────────── --}}
    <header class="cv-header flex items-start gap-4 pb-4 mb-6 border-b border-line">
        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-bold text-ink leading-tight">{{ $name }}</h1>
            @if($role)
                <p class="text-sm text-ink-muted mt-0.5">{{ $role }}</p>
            @endif
            @if($location)
                <p class="text-xs text-ink-muted mt-0.5">{{ $location }}</p>
            @endif

            {{-- Kontak dari tautan show_on_cv --}}
            @if($cvLinks->isNotEmpty())
                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1">
                    @foreach($cvLinks as $cvLink)
                        @php
                            $linkLabel = public_text($cvLink->label) ?? $cvLink->label;
                            // Jangan tampilkan nomor telepon di CV web
                            if ($cvLink->icon === 'phone') continue;
                        @endphp
                        <a
                            href="{{ $cvLink->url }}"
                            class="cv-contact-link text-xs text-ink-muted hover:text-ink"
                            target="_blank"
                            rel="noopener noreferrer"
                        >{{ $linkLabel }}</a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Foto kecil: hanya bila dua syarat terpenuhi --}}
        @if($showPhoto)
            <div class="cv-photo shrink-0 w-[28mm] h-[36mm] overflow-hidden border border-line rounded-sm bg-canvas-muted">
                <img
                    src="{{ media_url($siteSetting->photo) }}"
                    alt="{{ $name }}"
                    class="w-full h-full object-cover"
                    width="106"
                    height="136"
                >
            </div>
        @endif
    </header>

    {{-- ─── RINGKASAN ──────────────────────────────────────────────────────── --}}
    @php $summaryText = public_text($summary ?? ''); @endphp
    @if($summaryText)
        <section class="cv-section mb-5 break-inside-avoid">
            <h2 class="cv-section-title">{{ __('ui.cv_summary') }}</h2>
            <p class="text-sm text-ink leading-relaxed">{{ $summaryText }}</p>
        </section>
    @endif

    {{-- ─── PENGALAMAN KERJA ───────────────────────────────────────────────── --}}
    @if($workExps->isNotEmpty())
        <section class="cv-section mb-5">
            <h2 class="cv-section-title">{{ __('ui.cv_work_experience') }}</h2>
            @foreach($workExps as $exp)
                @php
                    $title = public_text($exp->title);
                    if (!$title) continue;
                @endphp
                <div class="cv-entry break-inside-avoid mb-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-ink leading-tight">{{ $title }}</p>
                            <p class="text-xs text-ink-muted">{{ $exp->organization }}{{ $exp->location ? ' · ' . $exp->location : '' }}</p>
                        </div>
                        <p class="text-xs text-ink-muted shrink-0 text-right">
                            {{ $exp->started_at?->translatedFormat('M Y') }}
                            – {{ $exp->ended_at ? $exp->ended_at->translatedFormat('M Y') : __('ui.present') }}
                        </p>
                    </div>
                    @php $desc = public_text($exp->description ?? ''); @endphp
                    @if($desc)
                        <div class="mt-1 text-xs text-ink-muted leading-relaxed">{!! Str::markdown($desc, ['html_input' => 'strip']) !!}</div>
                    @endif
                </div>
            @endforeach
        </section>
    @endif

    {{-- ─── MAGANG ─────────────────────────────────────────────────────────── --}}
    @if($internExps->isNotEmpty())
        <section class="cv-section mb-5">
            <h2 class="cv-section-title">{{ __('ui.cv_internship') }}</h2>
            @foreach($internExps as $exp)
                @php
                    $title = public_text($exp->title);
                    if (!$title) continue;
                @endphp
                <div class="cv-entry break-inside-avoid mb-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-ink leading-tight">{{ $title }}</p>
                            <p class="text-xs text-ink-muted">{{ $exp->organization }}{{ $exp->location ? ' · ' . $exp->location : '' }}</p>
                        </div>
                        <p class="text-xs text-ink-muted shrink-0 text-right">
                            {{ $exp->started_at?->translatedFormat('M Y') }}
                            – {{ $exp->ended_at ? $exp->ended_at->translatedFormat('M Y') : __('ui.present') }}
                        </p>
                    </div>
                    @php $desc = public_text($exp->description ?? ''); @endphp
                    @if($desc)
                        <div class="mt-1 text-xs text-ink-muted leading-relaxed">{!! Str::markdown($desc, ['html_input' => 'strip']) !!}</div>
                    @endif
                </div>
            @endforeach
        </section>
    @endif

    {{-- ─── ORGANISASI ─────────────────────────────────────────────────────── --}}
    @if($orgExps->isNotEmpty())
        <section class="cv-section mb-5">
            <h2 class="cv-section-title">{{ __('ui.cv_organization') }}</h2>
            @foreach($orgExps as $exp)
                @php
                    $title = public_text($exp->title);
                    if (!$title) continue;
                @endphp
                <div class="cv-entry break-inside-avoid mb-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-ink leading-tight">{{ $title }}</p>
                            <p class="text-xs text-ink-muted">{{ $exp->organization }}{{ $exp->location ? ' · ' . $exp->location : '' }}</p>
                        </div>
                        <p class="text-xs text-ink-muted shrink-0 text-right">
                            {{ $exp->started_at?->translatedFormat('M Y') }}
                            – {{ $exp->ended_at ? $exp->ended_at->translatedFormat('M Y') : __('ui.present') }}
                        </p>
                    </div>
                    @php $desc = public_text($exp->description ?? ''); @endphp
                    @if($desc)
                        <div class="mt-1 text-xs text-ink-muted leading-relaxed">{!! Str::markdown($desc, ['html_input' => 'strip']) !!}</div>
                    @endif
                </div>
            @endforeach
        </section>
    @endif

    {{-- ─── PENDIDIKAN ─────────────────────────────────────────────────────── --}}
    @if($eduExps->isNotEmpty())
        <section class="cv-section mb-5">
            <h2 class="cv-section-title">{{ __('ui.cv_education') }}</h2>
            @foreach($eduExps as $exp)
                @php
                    $orgName = $exp->organization;
                    $title = public_text($exp->title);
                @endphp
                <div class="cv-entry break-inside-avoid mb-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-ink leading-tight">{{ $orgName }}</p>
                            @if($title)
                                <p class="text-xs text-ink-muted">{{ $title }}</p>
                            @endif
                        </div>
                        <p class="text-xs text-ink-muted shrink-0 text-right">
                            {{ $exp->started_at?->format('Y') }}
                            – {{ $exp->ended_at ? $exp->ended_at->format('Y') : __('ui.present') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    {{-- ─── KEAHLIAN ───────────────────────────────────────────────────────── --}}
    @if(is_array($skills) && count($skills) > 0)
        <section class="cv-section mb-5 break-inside-avoid">
            <h2 class="cv-section-title">{{ __('ui.cv_skills') }}</h2>
            <div class="space-y-1">
                @foreach($skills as $skillGroup)
                    @php
                        $groupName = trim($skillGroup['group'] ?? '');
                        $items = array_filter(array_map('trim', explode(',', $skillGroup['items'] ?? '')));
                    @endphp
                    @if($groupName && count($items) > 0)
                        <div class="text-xs text-ink flex gap-2">
                            <span class="font-semibold shrink-0 text-ink-muted">{{ $groupName }}:</span>
                            <span>{{ implode(', ', array_values($items)) }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    {{-- ─── SERTIFIKAT ─────────────────────────────────────────────────────── --}}
    @if($certificates->isNotEmpty())
        <section class="cv-section mb-5 break-inside-avoid">
            <h2 class="cv-section-title">{{ __('ui.cv_certificates') }}</h2>
            <div class="space-y-1.5">
                @foreach($certificates as $cert)
                    <div class="cv-entry flex items-start justify-between gap-2 break-inside-avoid">
                        <div>
                            <span class="text-sm font-medium text-ink">{{ $cert->title }}</span>
                            @if($cert->issuer)
                                <span class="text-xs text-ink-muted ml-1">· {{ $cert->issuer }}</span>
                            @endif
                        </div>
                        @if($cert->issued_at)
                            <span class="text-xs text-ink-muted shrink-0">{{ $cert->issued_at->format('Y') }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</article>
</x-layouts.cv>
