<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Health Record Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Record for Batch: {{ $healthRecord->batch->batch_code }}</h3>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <p><strong>Recorded by:</strong> {{ $healthRecord->user->name }}</p>
                        <p><strong>Date:</strong> {{ $healthRecord->recorded_date->format('Y-m-d') }}</p>
                        <p><strong>Affected chickens:</strong> {{ $healthRecord->affected_count }}</p>
                        <p><strong>Dead chickens:</strong> {{ $healthRecord->dead_count }}</p>
                        <p><strong>Recovered chickens:</strong> {{ $healthRecord->recovered_count }}</p>
                        <p><strong>Recovery status:</strong> {{ str_replace('_', ' ', ucfirst($healthRecord->status)) }}</p>
                        <p><strong>Observation:</strong> {{ $healthRecord->observation }}</p>
                        <p><strong>Medication:</strong> {{ $healthRecord->medication_name ?? 'N/A' }}</p>
                        <p><strong>Dosage:</strong> {{ $healthRecord->dosage_amount ?? 'N/A' }} {{ $healthRecord->dosage_unit ?? '' }}</p>
                        <p><strong>Notes:</strong> {{ $healthRecord->notes ?? 'N/A' }}</p>
                        <p><strong>Remarks:</strong> {{ $healthRecord->remarks ?? 'N/A' }}</p>
                        <p><strong>Remedy:</strong> {{ $healthRecord->remedy ?? 'N/A' }}</p>
                    </div>

                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900">Health Record History</h4>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated by</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Affected</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dead</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recovered</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($healthRecord->history as $entry)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap">{{ $entry->created_at->format('Y-m-d H:i') }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">{{ $entry->user->name }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">{{ str_replace('_', ' ', ucfirst($entry->status)) }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">{{ $entry->affected_count }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">{{ $entry->dead_count }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">{{ $entry->recovered_count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ url()->previous() }}" class="text-blue-500 hover:underline">Back to list</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>