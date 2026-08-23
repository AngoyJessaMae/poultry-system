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
                                        <option value="{{ $batch->id }}" @if(($filters['batch_id'] ?? '') == $batch->id) selected @endif>{{ $batch->name }}</option>
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
                    <h3 class="text-lg font-medium text-gray-900">Analytics</h3>
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