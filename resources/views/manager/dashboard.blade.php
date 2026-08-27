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
                 <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center transition-transform transform hover:scale-105 hover:shadow-lg">
                    <div class="bg-blue-500 p-4 rounded-full">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Total Active Batches</h3>
                        <p class="mt-1 text-3xl font-semibold text-gray-700">{{ $totalActiveBatches }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center transition-transform transform hover:scale-105 hover:shadow-lg">
                    <div class="bg-red-500 p-4 rounded-full">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Today's Mortalities</h3>
                        <p class="mt-1 text-3xl font-semibold text-gray-700">{{ $todaysMortalityCount }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center transition-transform transform hover:scale-105 hover:shadow-lg">
                    <div class="bg-yellow-500 p-4 rounded-full">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Today's Feeding Logs</h3>
                        <p class="mt-1 text-3xl font-semibold text-gray-700">{{ $todaysFeedingLogsCount }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center transition-transform transform hover:scale-105 hover:shadow-lg">
                    <div class="bg-green-500 p-4 rounded-full">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org.I have updated the stat cards with a hover effect. Now, when you move your mouse over them, they will lift and scale slightly, making the dashboard more interactive.

What other improvements would you like to see on the dashboard? I can also enhance the "Batches Below Expected Performance" table to make it clearer and more visually organized.
svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01M12 6v-1m0-1V4m0 2v1m0 0v1m0-1.01V10m0 4.01V14m0 .01V15m0 1v.01M12 16v-1m0-1.01V14m0-4.01v.01M12 12.01V12m0-2.01V10m0-1V9m0-1V8m0 0h.01M12 8h-.01M11 8h1M12 16h.01M12 16h-.01M13 16h-1M12 12h.01M12 12h-.01M11 12h1M12 4.01V4m0-1V3m0 1V4m0 0v-.01M12 3v-.01M12 2.01V2M12 22v-1m0-1v-1m0 1v.01M12 21v-1m0 0v-.01M12 20v-1m0 0v-.01M12 19v-1m0 0v-.01M12 18v-1m0 0v-.01M12 17v-1m0 0v-.01M12 16.01V16m0-11V5m0-1V4m0 1V5m0 0v-.01M12 4v-.01M12 3.01V3M12 22.01V22M12 22v-1m0 1v.01M12 21v-1m0 0v-.01M12 20v-1m0 0v-.01M12 19v-1m0 0v-.01M12 18v-1m0 0v-.01M12 17v-1m0 0v-.01M12 16.01V16M4 12h.01M4 12h-.01M3 12h1M20 12h.01M20 12h-.01M21 12h-1M12 20.01V20M12 20v-1m0 1v.01M12 19v-1m0 0v-.01M12 18v-1m0 0v-.01M12 17v-1m0 0v-.01M12 16.01V16M12 8.01V8M12 8v-1m0 1v.01M12 7v-1m0 0v-.01M12 6v-1m0 0v-.01M12 5v-1m0 0v-.01M12 4.01V4"></path></svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">MTD Sales</h3>
                        <p class="mt-1 text-3xl font-semibold text-gray-700">${{ number_format($monthToDateSalesTotal, 2) }}</p>
                    </div>
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
                                    <tr class="border-b border-gray-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $batch->batch_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $batch->station->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $batch->current_age }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500">{{ $batch->below_expected_reason }}</td>
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