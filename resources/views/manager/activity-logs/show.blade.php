<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __(':name - Activity Details', ['name' => $user->name]) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <a href="{{ route('manager.activity-logs.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Back to Activity Overview</a>
                    </div>

                    <!-- Worker Information -->
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Worker Information</h3>
                        <p><strong>Name:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Status:</strong> 
                            @if($user->is_active)
                                <span class="text-green-600">Active</span>
                            @else
                                <span class="text-red-600">Inactive</span>
                            @endif
                        </p>
                        @if($user->currentSession && $user->currentSession->login_at->isToday())
                            <p><strong>Currently:</strong> <span class="text-green-600 font-semibold">Logged In since {{ $user->currentSession->login_at->format('H:i:s') }}</span></p>
                        @endif
                    </div>

                    <!-- Login Sessions Today -->
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Today's Login Sessions</h3>
                    @if($loginSessions->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200 mb-8">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Login Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logout Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($loginSessions as $session)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $session->login_at->format('H:i:s') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $session->logout_at ? $session->logout_at->format('H:i:s') : '<span class="text-green-600 font-semibold">Currently Active</span>' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($session->logout_at)
                                                {{ $session->login_at->diffForHumans($session->logout_at, true) }}
                                            @else
                                                {{ $session->login_at->diffForHumans() }} (ongoing)
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $session->ip_address }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="bg-gray-100 p-4 rounded mb-8">
                            <p class="text-gray-600">No login sessions recorded for today.</p>
                        </div>
                    @endif

                    <!-- All Activities Today -->
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">All Activities Today</h3>
                    @if($activityLogs->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($activityLogs as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $log->created_at->format('H:i:s') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($log->activity_type == 'login') bg-blue-100 text-blue-800
                                                @elseif($log->activity_type == 'logout') bg-gray-100 text-gray-800
                                                @elseif(str_contains($log->activity_type, 'create')) bg-green-100 text-green-800
                                                @elseif(str_contains($log->activity_type, 'update')) bg-yellow-100 text-yellow-800
                                                @elseif(str_contains($log->activity_type, 'delete')) bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($log->activity_type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">{{ $log->description ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $log->ip_address }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="bg-gray-100 p-4 rounded">
                            <p class="text-gray-600">No activities recorded for today.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-manager-layout>