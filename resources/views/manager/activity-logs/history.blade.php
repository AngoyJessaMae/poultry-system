<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Activity History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <a href="{{ route('manager.activity-logs.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Back to Today's Overview</a>
                    </div>

                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Login Sessions for {{ $date->format('F j, Y') }}</h3>
                    </div>

                    <!-- Filter Form -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <form method="GET" action="{{ route('manager.activity-logs.history') }}" class="flex flex-wrap gap-4 items-end">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="date" id="date" name="date" value="{{ $date->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700">Filter by Worker</label>
                                <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Workers</option>
                                    @foreach($workers as $worker)
                                        <option value="{{ $worker->id }}" {{ $userId == $worker->id ? 'selected' : '' }}>{{ $worker->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    @if($logs->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Worker</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Login Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logout Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($logs as $log)
                                    <tr class="{{ is_null($log->logout_at) ? 'bg-green-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $log->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $log->login_at->format('H:i:s') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($log->logout_at)
                                                {{ $log->logout_at->format('H:i:s') }}
                                            @else
                                                <span class="text-green-600 font-semibold">Still Active</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($log->logout_at)
                                                {{ $log->login_at->diffForHumans($log->logout_at, true) }}
                                            @else
                                                {{ $log->login_at->diffForHumans() }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('manager.activity-logs.show', $log->user) }}" class="text-indigo-600 hover:text-indigo-900">View Details</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $logs->appends(['date' => $date->format('Y-m-d'), 'user_id' => $userId])->links() }}
                        </div>
                    @else
                        <div class="bg-gray-100 p-4 rounded">
                            <p class="text-gray-600">No login sessions found for the selected date.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-manager-layout>