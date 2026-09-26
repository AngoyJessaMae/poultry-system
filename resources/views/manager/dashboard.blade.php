<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manager Dashboard') }}
        </h2>
    </x-slot>

    <div class="min-h-[calc(100vh-5rem)] bg-slate-50 py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-2xl px-6 py-8 shadow-lg sm:px-10" style="background: linear-gradient(135deg, #c2410c 0%, #ea580c 55%, #f59e0b 100%); color: #ffffff;">
                <div class="relative z-10 max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em]" style="color: #ffedd5;">{{ __('Management overview') }}</p>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl" style="color: #ffffff;">{{ __('Keep the farm moving.') }}</h1>
                    <p class="mt-3 max-w-xl text-sm leading-6 sm:text-base" style="color: #fff7ed;">{{ __('Review worker access and turn farm activity into clear, useful reports from one place.') }}</p>
                </div>
                <div class="absolute -right-10 -top-16 h-56 w-56 rounded-full border-[24px] border-white/10"></div>
                <div class="absolute -bottom-24 right-24 h-44 w-44 rounded-full border-[18px] border-white/10"></div>
            </div>

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
