<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Worker Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(!empty($missedFeedings))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Missed Feedings</p>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($missedFeedings as $schedule)
                            <li>
                                <strong>{{ $schedule->batch->name }}</strong> ({{ $schedule->batch->station->name }})
                                at {{ \Carbon\Carbon::parse($schedule->scheduled_time)->format('g:i A') }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Today's Feeding Schedule</h3>
                    <div class="mt-4 space-y-3">
                        @forelse($pendingFeedingSchedules as $schedule)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <span class="font-semibold">{{ $schedule->batch->name }}</span>
                                    <span class="text-sm text-gray-600">({{ $schedule->batch->station->name }}) - {{ \Carbon\Carbon::parse($schedule->scheduled_time)->format('g:i A') }}</span>
                                </div>
                                <a href="{{ route('worker.feeding-logs.create', ['batch_id' => $schedule->batch_id]) }}" class="px-3 py-1 bg-green-500 text-white text-sm font-semibold rounded-md hover:bg-green-600 transition">
                                    Log Feed
                                </a>
                            </div>
                        @empty
                            <p>No pending feeding schedules for today.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                <a href="{{ route('worker.feeding-logs.create') }}" class="bg-blue-500 text-white text-center font-bold py-4 px-6 rounded-lg hover:bg-blue-600 transition duration-300">
                    Log Feeding
                </a>
                <a href="{{ route('worker.growth-records.create') }}" class="bg-green-500 text-white text-center font-bold py-4 px-6 rounded-lg hover:bg-green-600 transition duration-300">
                    Log Growth
                </a>
                <a href="{{ route('worker.health-records.create') }}" class="bg-yellow-500 text-white text-center font-bold py-4 px-6 rounded-lg hover:bg-yellow-600 transition duration-300">
                    Log Health Issue
                </a>
                <a href="{{ route('worker.mortality-records.create') }}" class="bg-red-500 text-white text-center font-bold py-4 px-6 rounded-lg hover:bg-red-600 transition duration-300">
                    Log Mortality
                </a>
                <a href="{{ route('worker.sales.create') }}" class="bg-purple-500 text-white text-center font-bold py-4 px-6 rounded-lg hover:bg-purple-600 transition duration-300">
                    Log Sale
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