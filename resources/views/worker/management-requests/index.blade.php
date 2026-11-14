<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Management Requests</h2></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8"><div class="bg-white shadow-sm sm:rounded-lg p-6">
        @if(session('success'))<div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>@endif
        <table class="min-w-full divide-y divide-gray-200"><thead><tr><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Identifier</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Reviewed</th></tr></thead>
            <tbody class="divide-y divide-gray-200">@forelse($managementRequests as $item)<tr>
                <td class="px-4 py-3">{{ ucfirst($item->request_type) }}</td>
                <td class="px-4 py-3">{{ $item->payload[$item->request_type === 'station' ? 'station_code' : 'batch_code'] ?? '—' }}</td>
                <td class="px-4 py-3">{{ ucfirst($item->status) }}</td>
                <td class="px-4 py-3">{{ $item->reviewed_at?->format('Y-m-d H:i') ?? 'Pending manager review' }}</td>
            </tr>@empty<tr><td colspan="4" class="px-4 py-3">No requests submitted.</td></tr>@endforelse</tbody>
        </table>
    </div></div></div>
</x-app-layout>
