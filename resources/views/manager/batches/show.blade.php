<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Batch Details') }}
            </h2>
            <a href="{{ route('manager.batches.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                Back to Batches
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">{{ $batch->batch_code }}</h3>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <p><strong>Station:</strong> {{ $batch->station->name }}</p>
                        <p><strong>Arrival Date:</strong> {{ $batch->arrival_date->format('Y-m-d') }}</p>
                        <p><strong>Age:</strong> {{ $batch->current_age }} days</p>
                        <p><strong>Feeding Method:</strong> {{ str_replace('_', ' ', Str::title($batch->feeding_method)) }}</p>
                        <p><strong>Initial Quantity:</strong> {{ $batch->initial_quantity }}</p>
                        <p><strong>Current Quantity:</strong> {{ $batch->current_quantity }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Movement History</h3>
                    <ul class="divide-y divide-gray-200 mt-4">
                        @forelse($batch->movements as $movement)
                            <li class="py-4">
                                <p class="text-sm text-gray-700">
                                    Moved from <span class="font-medium">{{ $movement->fromStation->name ?? 'N/A' }}</span>
                                    to <span class="font-medium">{{ $movement->toStation->name ?? 'N/A' }}</span>
                                    by <span class="font-medium">{{ $movement->user->name ?? 'System' }}</span>
                                </p>
                                <p class="text-sm text-gray-500">{{ $movement->moved_at->format('M d, Y g:i A') }}</p>
                            </li>
                        @empty
                            <p>No movement history for this batch.</p>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Batch Timeline</h3>
                    <div class="mt-4">
                        @php
                            $timeline = collect()
                                ->concat($batch->feedingLogs)
                                ->concat($batch->growthRecords)
                                ->concat($batch->healthRecords)
                                ->concat($batch->mortalityRecords)
                                ->concat($batch->sales)
                                ->concat($batch->movements)
                                ->sortByDesc('created_at');
                        @endphp
                        <ul class="divide-y divide-gray-200">
                            @forelse($timeline as $item)
                                <li class="py-4">
                                    <div class="flex space-x-3">
                                        <div class="flex-1 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-sm font-medium">{{ class_basename($item) }}</h3>
                                                <p class="text-sm text-gray-500">{{ $item->created_at->format('M d, Y g:i A') }}</p>
                                            </div>
                                            <p class="text-sm text-gray-500">
                                                @switch(class_basename($item))
                                                    @case('FeedingLog')
                                                        Fed {{ $item->quantity }} kg of {{ $item->feed_type }}.
                                                        @break
                                                    @case('GrowthRecord')
                                                        Average weight: {{ number_format($item->avg_weight, 2) }} g.
                                                        @break
                                                    @case('HealthRecord')
                                                        Observation: {{ $item->observation }}.
                                                        @break
                                                    @case('MortalityRecord')
                                                        {{ $item->count }} mortalities recorded.
                                                        @break
                                                    @case('Sale')
                                                        {{ $item->heads_sold }} heads sold for ${{ number_format($item->total_amount, 2) }}.
                                                        @break
                                                    @case('BatchMovement')
                                                        Moved from {{ $item->fromStation->name ?? 'N/A' }} to {{ $item->toStation->name ?? 'N/A' }}.
                                                        @break
                                                @endswitch
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <p>No records found for this batch.</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>