<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Batches') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Batches List</h3>
                        <a href="{{ route('manager.batches.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Add Batch
                        </a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Station</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrival Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Initial Quantity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Quantity</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($batches as $batch)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $batch->batch_code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $batch->station->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $batch->arrival_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $batch->initial_quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $batch->current_quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('manager.batches.show', $batch) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        <a href="{{ route('manager.batches.edit', $batch) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
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