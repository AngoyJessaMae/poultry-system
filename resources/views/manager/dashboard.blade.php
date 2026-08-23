<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manager Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Stat Cards -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900">Total Active Batches</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-700">{{ $totalActiveBatches }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900">Today's Mortalities</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-700">{{ $todaysMortalityCount }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900">Today's Feeding Logs</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-700">{{ $todaysFeedingLogsCount }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900">MTD Sales</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-700">${{ number_format($monthToDateSalesTotal, 2) }}</p>
                </div>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Batches Below Expected Performance</h3>
                    <div class="mt-4">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Station</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age (Days)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($batchesBelowExpected as $batch)
                                    <tr class="bg-red-100">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $batch->batch_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $batch->station->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $batch->current_age }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $batch->below_expected_reason }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>