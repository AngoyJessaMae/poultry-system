<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daily Production Reports</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Daily Production Report</h3>
                            <p class="mt-1 text-sm text-gray-500">Choose a flock to enter daily records, review calculated standards, or print the production form.</p>
                        </div>
                        <a href="{{ route('manager.reports.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">Back to Reports</a>
                    </div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Farm</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loading Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Flockman</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($batches as $batch)
                                    <tr>
                                        <td class="px-4 py-3">{{ $batch->batch_code }}</td>
                                        <td class="px-4 py-3">{{ config('farm.name') }}</td>
                                        <td class="px-4 py-3">{{ $batch->arrival_date->format('Y-m-d') }}</td>
                                        <td class="px-4 py-3">{{ $batch->flockman?->name ?: 'Not assigned' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a class="inline-flex items-center px-3 py-2 bg-orange-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600" href="{{ route('reports.daily.show', $batch) }}">Open report</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No batches found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>