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
                    @if ($errors->has('batch'))
                        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first('batch') }}
                        </div>
                    @endif

                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Batches List</h3>
                        <a href="{{ route('worker.batches.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Add Batch
                        </a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Station</th>
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
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $bgColor = match($batch->growth_stage) {
                                                'Chick' => 'bg-yellow-200',
                                                'Grower' => 'bg-blue-200',
                                                'Market-Ready' => 'bg-green-200',
                                                'Underweight' => 'bg-red-200',
                                                default => '',
                                            };
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bgColor }}">
                                            {{ $batch->growth_stage }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $batch->station->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $batch->current_quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('worker.batches.show', $batch) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        <a href="{{ route('worker.batches.edit', $batch) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                                        <form action="{{ route('worker.batches.destroy', $batch) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 ml-4">Delete</button>
                                        </form>
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