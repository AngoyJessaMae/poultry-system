<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Worker Dashboard') }}
        </h2>
    </x-slot>

    <div class="min-h-[calc(100vh-5rem)] bg-slate-50 py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-gradient-to-r from-orange-700 to-amber-500 p-6 text-white shadow-lg sm:p-8"><p class="text-sm font-semibold uppercase tracking-[0.18em] text-orange-100">Daily operations</p><h1 class="mt-2 text-3xl font-bold">Good day, {{ auth()->user()->name }}</h1><p class="mt-2 max-w-2xl text-sm text-orange-50">Record feeding, growth, health, and mortality from one place. Manager-only finance and sales controls remain hidden.</p></div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Assigned stations</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $assignedStations->count() }}</p><p class="text-xs text-slate-500">S1, S2, or S3</p></div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Active batches</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $activeBatches->count() }}</p><p class="text-xs text-slate-500">Ready for daily records</p></div>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm"><p class="text-sm text-amber-700">My pending requests</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $pendingRequests }}</p><a href="{{ route('worker.management-requests.index') }}" class="text-xs font-semibold text-amber-800 hover:underline">View request status →</a></div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">@foreach($assignedStations as $station)<a href="{{ route('worker.feeding-logs.station', $station) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-300 hover:shadow-md"><div class="flex items-center justify-between"><div><p class="text-xs font-bold uppercase tracking-widest text-orange-600">{{ $station->station_code }}</p><h3 class="mt-1 text-lg font-bold text-slate-900">{{ $station->name }}</h3></div><span class="text-2xl text-orange-500">→</span></div><p class="mt-4 text-sm text-slate-500">{{ $station->batches()->where('status', 'active')->count() }} active batches · open feeding records</p></a>@endforeach</div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Active Batches</h3>
                    <div class="mt-4 space-y-3">
                        @forelse($activeBatches as $batch)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <span class="font-semibold">{{ $batch->name }}</span>
                                    <span class="text-sm text-gray-600">({{ $batch->station->name }})</span>
                                </div>
                                <a href="{{ route('worker.batches.feeding-logs.create', $batch) }}" class="px-3 py-1 bg-green-500 text-white text-sm font-semibold rounded-md hover:bg-green-600 transition">
                                    Log Feed
                                </a>
                            </div>
                        @empty
                            <p>No active batches.</p>
                        @endforelse
                    </div>
                </div>
            </div>



            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <a href="{{ route('worker.growth-records.create') }}" class="bg-green-500 text-white text-center font-bold py-4 px-6 rounded-lg hover:bg-green-600 transition duration-300">
                    Log Growth
                </a>
                <a href="{{ route('worker.health-records.create') }}" class="bg-yellow-500 text-white text-center font-bold py-4 px-6 rounded-lg hover:bg-yellow-600 transition duration-300">
                    Log Health Issue
                </a>
                <a href="{{ route('worker.mortality-records.create') }}" class="bg-red-500 text-white text-center font-bold py-4 px-6 rounded-lg hover:bg-red-600 transition duration-300">
                    Log Mortality
                </a>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Your Recent Entries (Last 24 Hours)</h3>
                    <div class="mt-4">
                        <ul class="divide-y divide-gray-200">
                            @forelse($recentEntries as $entry)
                                <li class="py-4 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ class_basename($entry) }}</p>
                                        <p class="text-sm text-gray-500">{{ $entry->created_at->diffForHumans() }}</p>
                                    </div>
                                    <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">View</a>
                                </li>
                            @empty
                                <li>No recent entries.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
