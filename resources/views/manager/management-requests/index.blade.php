<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Station and Batch Approval Requests</h2></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8"><div class="bg-white shadow-sm sm:rounded-lg p-6">
        @if(session('success'))<div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>@endif
        <table class="min-w-full divide-y divide-gray-200"><thead><tr><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Identifier</th><th class="px-4 py-3 text-left">Requested By</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3"></th></tr></thead>
            <tbody class="divide-y divide-gray-200">@forelse($managementRequests as $item)<tr>
                <td class="px-4 py-3">{{ ucfirst($item->request_type) }}</td>
                <td class="px-4 py-3">{{ $item->payload[$item->request_type === 'station' ? 'station_code' : 'batch_code'] ?? '—' }}</td>
                <td class="px-4 py-3">{{ $item->requester->name }}</td>
                <td class="px-4 py-3">{{ ucfirst($item->status) }}</td>
                <td class="px-4 py-3 text-right">@if($item->status === 'pending')
                    <form method="POST" action="{{ route('manager.management-requests.approve', $item) }}" class="inline">@csrf<button class="text-green-700 mr-3">Approve</button></form>
                    <form method="POST" action="{{ route('manager.management-requests.reject', $item) }}" class="inline">@csrf<button class="text-red-700">Reject</button></form>
                @else {{ $item->reviewed_at?->format('Y-m-d H:i') }} @endif</td>
            </tr>@empty<tr><td colspan="5" class="px-4 py-3">No requests found.</td></tr>@endforelse</tbody>
        </table>
    </div></div></div>
</x-app-layout>
