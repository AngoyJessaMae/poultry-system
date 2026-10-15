<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manager Dashboard') }}
        </h2>
    </x-slot>

    <div class="min-h-[calc(100vh-5rem)] bg-slate-50 py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <section
                class="relative min-h-[24rem] overflow-hidden rounded-2xl bg-slate-950 shadow-lg"
                x-data="{
                    activeSlide: 0,
                    slides: [
                        {
                            image: '{{ asset('images/1Day.jpg') }}',
                            eyebrow: 'Day 1 flock',
                            title: 'A strong start for every chick.',
                            description: 'Monitor the first day closely and establish a healthy routine from the beginning.'
                        },
                        {
                            image: '{{ asset('images/15Days.jpg') }}',
                            eyebrow: 'Day 15 flock',
                            title: 'Steady routines build stronger birds.',
                            description: 'Track growth, feeding, and health as the flock moves through its grower stage.'
                        },
                        {
                            image: '{{ asset('images/21Days.jpg') }}',
                            eyebrow: 'Day 21 flock',
                            title: 'Small signals, better outcomes.',
                            description: 'Use the dashboard to spot activity patterns as the flock approaches market readiness.'
                        }
                    ],
                    next() { this.activeSlide = (this.activeSlide + 1) % this.slides.length },
                    previous() { this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length }
                }"
                aria-label="Chicken farm highlights"
            >
                <div class="absolute inset-0 z-0 bg-[linear-gradient(90deg,rgba(124,45,18,0.98)_0%,rgba(194,65,12,0.92)_40%,rgba(234,88,12,0.12)_100%)]"></div>
                <div class="relative z-10 flex min-h-[24rem] items-center px-6 py-8 sm:px-10 lg:px-14">
                    <div class="w-1/2 max-w-md pr-4 sm:pr-6">
                        <div class="overflow-hidden">
                            <div class="flex min-h-[13rem] w-full items-center transition-transform duration-700 ease-out" :style="`transform: translateX(-${activeSlide * 100}%);`">
                                <template x-for="slide in slides" :key="slide.title">
                                    <div class="w-full min-w-full flex-none">
                                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-300" x-text="slide.eyebrow"></p>
                                        <h1 class="mt-3 text-3xl font-bold tracking-tight text-white sm:text-4xl" x-text="slide.title"></h1>
                                        <p class="mt-4 text-sm leading-6 text-slate-200 sm:text-base" x-text="slide.description"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center gap-3">
                            <button type="button" @click="previous()" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/30 text-white transition hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-amber-300" aria-label="Previous image">
                                <span aria-hidden="true">&larr;</span>
                            </button>
                            <button type="button" @click="next()" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/30 text-white transition hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-amber-300" aria-label="Next image">
                                <span aria-hidden="true">&rarr;</span>
                            </button>
                            <div class="ml-2 flex gap-2" aria-hidden="true">
                                <template x-for="(slide, index) in slides" :key="index">
                                    <button type="button" @click="activeSlide = index" class="h-2 rounded-full transition-all" :class="activeSlide === index ? 'w-8 bg-amber-300' : 'w-2 bg-white/50'"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="absolute inset-y-0 right-0 z-[1] w-1/2 overflow-hidden lg:w-[42%]">
                    <div class="flex h-full w-full transition-transform duration-700 ease-out" :style="`transform: translateX(-${activeSlide * 100}%);`">
                        <template x-for="slide in slides" :key="slide.image">
                            <img
                                :src="slide.image"
                                :alt="slide.title"
                                class="h-full min-w-full flex-none object-cover object-center"
                            >
                        </template>
                    </div>
                </div>
            </section>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-amber-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-slate-500">{{ __('Pending approvals') }}</p>
                        <span class="rounded-lg bg-amber-100 p-2 text-amber-700" aria-hidden="true">!</span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $pendingWorkers }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ __('Workers waiting for review') }}</p>
                </div>

                <div class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-slate-500">{{ __('Active workers') }}</p>
                        <span class="rounded-lg bg-emerald-100 p-2 text-emerald-700" aria-hidden="true">&#10003;</span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $activeWorkers }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ __('Approved accounts with access') }}</p>
                </div>

                <div class="rounded-xl border border-sky-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-slate-500">{{ __('Generated reports') }}</p>
                        <span class="rounded-lg bg-sky-100 p-2 text-sky-700" aria-hidden="true">&#9776;</span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $generatedReports }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ __('Reports saved in the system') }}</p>
                </div>
            </div>

            <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="activity-trend-heading">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-orange-600">{{ __('Farm activity') }}</p>
                        <h2 id="activity-trend-heading" class="mt-1 text-2xl font-bold text-slate-900">{{ __('Last 7 days') }}</h2>
                    </div>
                    <p class="text-sm text-slate-500"><span class="font-semibold text-slate-900">{{ $activityTrendTotal }}</span> {{ __('records logged') }}</p>
                </div>

                @php
                    $chartWidth = 520;
                    $chartTop = 20;
                    $chartBottom = 190;
                    $chartHeight = $chartBottom - $chartTop;
                    $chartStep = $chartWidth / max(1, count($activityTrend) - 1);
                    $chartLines = [
                        ['key' => 'feeding', 'label' => __('Feeding'), 'stroke' => '#f97316'],
                        ['key' => 'growth', 'label' => __('Growth'), 'stroke' => '#10b981'],
                        ['key' => 'health', 'label' => __('Health'), 'stroke' => '#0ea5e9'],
                        ['key' => 'mortality', 'label' => __('Mortality'), 'stroke' => '#f43f5e'],
                        ['key' => 'sales', 'label' => __('Sales'), 'stroke' => '#8b5cf6'],
                    ];
                @endphp
                <div class="mt-8 overflow-x-auto" role="img" aria-label="Seven-day farm activity line graph">
                    <svg class="h-auto min-w-[34rem] w-full" viewBox="0 0 580 240" preserveAspectRatio="none" aria-hidden="true">
                        <line x1="35" y1="20" x2="555" y2="20" stroke="#e2e8f0" stroke-width="1" />
                        <line x1="35" y1="105" x2="555" y2="105" stroke="#e2e8f0" stroke-width="1" />
                        <line x1="35" y1="190" x2="555" y2="190" stroke="#cbd5e1" stroke-width="1" />
                        <text x="0" y="24" fill="#94a3b8" font-size="11">{{ $activityTrendMax }}</text>
                        <text x="9" y="109" fill="#94a3b8" font-size="11">{{ number_format($activityTrendMax / 2, 1) }}</text>
                        <text x="25" y="194" fill="#94a3b8" font-size="11">0</text>

                        @foreach($chartLines as $line)
                            @php
                                $points = collect($activityTrend)->values()->map(function ($day, $index) use ($line, $chartStep, $chartBottom, $chartHeight, $activityTrendMax) {
                                    $x = 35 + ($index * $chartStep);
                                    $y = $chartBottom - (($day[$line['key']] / $activityTrendMax) * $chartHeight);
                                    return number_format($x, 2, '.', '') . ',' . number_format($y, 2, '.', '');
                                })->implode(' ');
                            @endphp
                            <polyline points="{{ $points }}" fill="none" stroke="{{ $line['stroke'] }}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                            @foreach($activityTrend as $index => $day)
                                @php
                                    $pointX = 35 + ($index * $chartStep);
                                    $pointY = $chartBottom - (($day[$line['key']] / $activityTrendMax) * $chartHeight);
                                @endphp
                                <circle cx="{{ $pointX }}" cy="{{ $pointY }}" r="4" fill="white" stroke="{{ $line['stroke'] }}" stroke-width="2">
                                    <title>{{ $line['label'] }}: {{ $day[$line['key']] }} on {{ $day['date'] }}</title>
                                </circle>
                            @endforeach
                        @endforeach

                        @foreach($activityTrend as $index => $day)
                            @php $labelX = 35 + ($index * $chartStep); @endphp
                            <text x="{{ $labelX }}" y="216" text-anchor="middle" fill="#475569" font-size="11">{{ $day['label'] }}</text>
                            <text x="{{ $labelX }}" y="232" text-anchor="middle" fill="#94a3b8" font-size="10">{{ $day['date'] }}</text>
                        @endforeach
                    </svg>
                </div>

                <div class="mt-6 flex flex-wrap gap-x-5 gap-y-2 text-xs text-slate-600">
                    @foreach($chartLines as $line)
                        <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $line['stroke'] }}"></span>{{ $line['label'] }}</span>
                    @endforeach
                </div>
            </section>

            <div class="mt-8 flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-orange-600">{{ __('Quick actions') }}</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">{{ __('What would you like to do?') }}</h2>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-5 lg:grid-cols-2">
                <a href="{{ route('manager.users.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-orange-200 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 sm:p-8">
                    <div class="flex items-start justify-between">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-xl text-orange-700" aria-hidden="true">&#9787;</span>
                        <span class="text-2xl text-slate-300 transition group-hover:translate-x-1 group-hover:text-orange-500" aria-hidden="true">&rarr;</span>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-900">{{ __('Manage users') }}</h3>
                    <p class="mt-2 max-w-md text-sm leading-6 text-slate-600">{{ __('Approve new worker registrations, update account details, or review active workers.') }}</p>
                    <span class="mt-6 inline-flex items-center text-sm font-semibold text-orange-700">{{ __('Open user management') }} <span class="ml-2" aria-hidden="true">&rarr;</span></span>
                </a>

                <a href="{{ route('manager.reports.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-sky-200 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 sm:p-8">
                    <div class="flex items-start justify-between">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-xl text-sky-700" aria-hidden="true">&#9776;</span>
                        <span class="text-2xl text-slate-300 transition group-hover:translate-x-1 group-hover:text-sky-500" aria-hidden="true">&rarr;</span>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-900">{{ __('View reports') }}</h3>
                    <p class="mt-2 max-w-md text-sm leading-6 text-slate-600">{{ __('Filter farm activity, review performance analytics, and export report data when needed.') }}</p>
                    <span class="mt-6 inline-flex items-center text-sm font-semibold text-sky-700">{{ __('Open reports') }} <span class="ml-2" aria-hidden="true">&rarr;</span></span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
