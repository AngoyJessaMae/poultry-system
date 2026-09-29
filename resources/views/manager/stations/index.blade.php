<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($errors->has('station'))
                        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first('station') }}
                        </div>
                    @endif

                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Stations List</h3>
                        @if(auth()->user()->isManager())
                            <a href="{{ route('manager.stations.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Add Station
                            </a>
                        @endif
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Station ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age Range (Days)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($stations as $station)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $station->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $station->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $station->capacity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $station->min_age_days }} - {{ $station->max_age_days }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $station->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if(auth()->user()->isManager())
                                        <a href="{{ route('manager.stations.show', $station) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            <a href="{{ route('manager.stations.edit', $station) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                                            <form action="{{ route('manager.stations.destroy', $station) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type-="submit" class="text-red-600 hover:text-red-900 ml-4">Delete</button>
                                            </form>
                                        @endif
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