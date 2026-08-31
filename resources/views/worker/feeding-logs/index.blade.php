<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Feeding Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Upcoming Feedings</h3>
                        <a href="{{ route('worker.feeding-logs.select-batch') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">Add New Feeding Log</a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Action</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($schedules as $schedule)
                                @php
                                    $rowClass = '';
                                    if ($schedule->status === 'missed') {
                                        $rowClass = 'bg-red-100';
                                    } elseif ($schedule->status === 'due') {
                                        $rowClass = 'bg-yellow-100';
                                    } elseif ($schedule->status === 'fed') {
                                        $rowClass = 'bg-green-100';
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $schedule->batch_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $schedule->schedule_time }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $schedule->type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($schedule->status) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($schedule->status === 'due' || $schedule->type === 'Unlimited')
                                            <a href="{{ route('worker.batches.feeding-logs.create', $schedule->batch_id) }}" class="text-indigo-600 hover:text-indigo-900">Feed</a>
                                        @elseif($schedule->status === 'fed')
                                            <span class="text-gray-500">Completed</span>
                                        @elseif($schedule->status === 'missed')
                                            <span class="text-red-500">Missed</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="flex justify-between mt-8 mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Feeding History</h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feed Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($feedingLogs as $log)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($log->feeding_time)->format('Y-m-d g:i A') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $log->batch->batch_code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $log->feed_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $log->quantity }} kg</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('worker.feeding-logs.edit', $log) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>