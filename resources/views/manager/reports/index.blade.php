<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('manager.reports.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <x-input-label for="start_date" :value="__('Start Date')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="$filters['start_date'] ?? ''" />
                            </div>
                            <div>
                                <x-input-label for="end_date" :value="__('End Date')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="$filters['end_date'] ?? ''" />
                            </div>
                            <div>
                                <x-input-label for="station_id" :value="__('Station')" />
                                <select id="station_id" name="station_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All Stations</option>
                                    @foreach($stations as $station)
                                        <option value="{{ $station->id }}" @if(($filters['station_id'] ?? '') == $station->id) selected @endif>{{ $station->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="batch_id" :value="__('Batch')" />
                                <select id="batch_id" name="batch_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All Batches</option>
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch->id }}" @if(($filters['batch_id'] ?? '') == $batch->id) selected @endif>{{ $batch->batch_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="report_type" :value="__('Report Type')" />
                                <select id="report_type" name="report_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="all" @if(($filters['report_type'] ?? '') == 'all') selected @endif>All</option>
                                    <option value="feeding" @if(($filters['report_type'] ?? '') == 'feeding') selected @endif>Feeding</option>
                                    <option value="growth" @if(($filters['report_type'] ?? '') == 'growth') selected @endif>Growth</option>
                                    <option value="mortality" @if(($filters['report_type'] ?? '') == 'mortality') selected @endif>Mortality</option>
                                    <option value="sales" @if(($filters['report_type'] ?? '') == 'sales') selected @endif>Sales</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Filter') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Core Analytics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h4 class="text-gray-600">Feed-to-Weight Ratio</h4>
                            <p class="text-2xl font-bold">{{ number_format($analytics['feedToWeightRatio'], 2) }}</p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h4 class="text-gray-600">Mortality Rate</h4>
                            <p class="text-2xl font-bold">{{ number_format($analytics['mortalityRate'], 2) }}%</p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h4 class="text-gray-600">Total Sales</h4>
                            <p class="text-2xl font-bold">₱{{ number_format($analytics['totalSales'], 2) }}</p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h4 class="text-gray-600">Profit Difference</h4>
                            <p class="text-2xl font-bold {{ $analytics['profitDifference'] >= 0 ? 'text-green-500' : 'text-red-500' }}">{{ number_format($analytics['profitDifference'], 2) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Overall Activity Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-{{ 
                        ($filters['report_type'] ?? 'all') === 'feeding' ? '3' : 
                        (($filters['report_type'] ?? '') === 'growth' ? '2' : 
                        (($filters['report_type'] ?? '') === 'mortality' ? '2' : 
                        (($filters['report_type'] ?? '') === 'sales' ? '2' : '6'))) 
                    }} gap-4 mt-4">
                        @if(($filters['report_type'] ?? 'all') === 'all' || ($filters['report_type'] ?? '') === 'feeding')
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-gray-600 text-sm">Total Feed Used</h4>
                            <p class="text-2xl font-bold">{{ number_format($analytics['totalFeedUsed'], 2) }} kg</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="text-gray-600 text-sm">Feeding Logs</h4>
                            <p class="text-2xl font-bold">{{ $analytics['totalFeedingLogs'] }}</p>
                        </div>
                        @endif
                        @if(($filters['report_type'] ?? 'all') === 'all' || ($filters['report_type'] ?? '') === 'growth')
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h4 class="text-gray-600 text-sm">Growth Records</h4>
                            <p class="text-2xl font-bold">{{ $analytics['totalGrowthRecords'] }}</p>
                        </div>
                        @endif
                        @if(($filters['report_type'] ?? 'all') === 'all' || ($filters['report_type'] ?? '') === 'mortality')
                        <div class="bg-red-50 p-4 rounded-lg">
                            <h4 class="text-gray-600 text-sm">Mortality Records</h4>
                            <p class="text-2xl font-bold">{{ $analytics['totalMortalityRecords'] }}</p>
                        </div>
                        @endif
                        @if(($filters['report_type'] ?? 'all') === 'all' || ($filters['report_type'] ?? '') === 'sales')
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h4 class="text-gray-600 text-sm">Sales Records</h4>
                            <p class="text-2xl font-bold">{{ $analytics['totalSalesRecords'] }}</p>
                        </div>
                        @endif
                        @if(($filters['report_type'] ?? 'all') === 'all' || ($filters['report_type'] ?? '') === 'feeding')
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <h4 class="text-gray-600 text-sm">Active Workers</h4>
                            <p class="text-2xl font-bold">{{ $analytics['uniqueWorkers'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if(($filters['report_type'] ?? 'all') === 'all' || ($filters['report_type'] ?? '') === 'feeding')
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Worker Feeding Activity</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Worker Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Feedings</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Feed Distributed (kg)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Feed per Feeding (kg)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Debug: Total feeding logs: {{ $analytics['totalFeedingLogs'] }}, Worker records: {{ $analytics['workerFeedingActivity']->count() }} -->
                                    @forelse($analytics['workerFeedingActivity'] as $worker)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $worker->worker_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $worker->total_feedings }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($worker->total_feed_kg, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($worker->total_feed_kg / $worker->total_feedings, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No worker feeding activity found. Total feeding logs in period: {{ $analytics['totalFeedingLogs'] }}
                                            @if($analytics['totalFeedingLogs'] > 0)
                                                <br>This might be because user_ids are not set on the feeding logs, or there's a database relationship issue.
                                            @endif
                                        </td>
                                    </tr>
                                    @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Detailed Records Table -->
            @if(($filters['report_type'] ?? 'all') !== 'all')
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ ucfirst($filters['report_type'] ?? '') }} Records
                    </h3>
                    <div class="mt-4 overflow-x-auto">
                        @if($data->isEmpty())
                            <p class="text-center text-sm text-gray-500 py-8">No {{ $filters['report_type'] ?? '' }} records found for the selected period</p>
                        @else
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @if($filters['report_type'] === 'feeding')
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Fed</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Worker</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Station</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feed (kg)</th>
                                        @elseif($filters['report_type'] === 'growth')
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Recorded</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Weight (g)</th>
                                        @elseif($filters['report_type'] === 'mortality')
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Station</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cause</th>
                                        @elseif($filters['report_type'] === 'sales')
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heads Sold</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($data as $record)
                                    <tr>
                                        @if($filters['report_type'] === 'feeding')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->fed_at->format('Y-m-d') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->user->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->station->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->batch->batch_code ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($record->quantity_kg, 2) }}</td>
                                        @elseif($filters['report_type'] === 'growth')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->recorded_date->format('Y-m-d') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->batch->batch_code ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->average_weight_grams }}</td>
                                        @elseif($filters['report_type'] === 'mortality')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->mortality_date->format('Y-m-d') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->station->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->batch->batch_code ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->count }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->suspected_cause ?? 'N/A' }}</td>
                                        @elseif($filters['report_type'] === 'sales')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->sale_date->format('Y-m-d') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->batch->batch_code ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->heads_sold }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($record->total_amount, 2) }}</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="mt-8 flex justify-end">
                <a href="{{ route('manager.reports.export.pdf', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Export to PDF
                </a>
                <a href="{{ route('manager.reports.export.excel', request()->query()) }}" class="ms-4 inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Export to Excel
                </a>
            </div>
        </div>
    </div>
</x-app-layout>