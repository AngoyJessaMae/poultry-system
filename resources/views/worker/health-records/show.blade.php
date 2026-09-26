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
                    <h3 class="text-lg font-medium text-gray-900">Record for Batch: {{ $healthRecord->batch->name }}</h3>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <p><strong>Recorded by:</strong> {{ $healthRecord->user->name }}</p>
                        <p><strong>Date:</strong> {{ $healthRecord->recorded_date->format('Y-m-d') }}</p>
                        <p><strong>Affected chickens:</strong> {{ $healthRecord->affected_count }}</p>
                        <p><strong>Dead chickens:</strong> {{ $healthRecord->dead_count }}</p>
                        <p><strong>Recovery status:</strong> {{ str_replace('_', ' ', ucfirst($healthRecord->status)) }}</p>
                        <p><strong>Observation:</strong> {{ $healthRecord->observation }}</p>
                        <p><strong>Medication:</strong> {{ $healthRecord->medication_name ?? 'N/A' }}</p>
                        <p><strong>Dosage:</strong> {{ $healthRecord->dosage_amount ?? 'N/A' }} {{ $healthRecord->dosage_unit ?? '' }}</p>
                        <p><strong>Notes:</strong> {{ $healthRecord->notes ?? 'N/A' }}</p>
                        <p><strong>Remarks:</strong> {{ $healthRecord->remarks ?? 'N/A' }}</p>
                        <p><strong>Remedy:</strong> {{ $healthRecord->remedy ?? 'N/A' }}</p>
                    </div>
                    <div class="mt-6">
                        <a href="{{ url()->previous() }}" class="text-blue-500 hover:underline">Back to list</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>