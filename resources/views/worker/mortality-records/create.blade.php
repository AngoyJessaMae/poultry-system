<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Log Mortality Record') }}
            </h2>
            <a href="{{ route('worker.mortality-records.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Back to Mortality Records') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('worker.mortality-records.store') }}">
                        @csrf

                        <!-- Batch -->
                        <div class="mt-4">
                            <x-input-label for="batch_id" :value="__('Batch')" />
                            <select id="batch_id" name="batch_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select a batch</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}" data-station-id="{{ $batch->station_id }}" data-station-name="{{ $batch->station->name }}">{{ $batch->batch_code }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
                        </div>

                        <!-- Station (Read-only) -->
                        <div class="mt-4">
                            <x-input-label for="station_name" :value="__('Station')" />
                            <x-text-input id="station_name" class="block mt-1 w-full bg-gray-100" type="text" name="station_name" readonly />
                            <input type="hidden" id="station_id" name="station_id">
                            <x-input-error :messages="$errors->get('station_id')" class="mt-2" />
                        </div>

                        <!-- Mortality Date -->
                        <div class="mt-4">
                            <x-input-label for="mortality_date" :value="__('Mortality Date')" />
                            <x-text-input id="mortality_date" class="block mt-1 w-full" type="date" name="mortality_date" :value="old('mortality_date', now()->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('mortality_date')" class="mt-2" />
                        </div>

                        <!-- Count -->
                        <div class="mt-4">
                            <x-input-label for="count" :value="__('Number of Birds')" />
                            <x-text-input id="count" class="block mt-1 w-full" type="number" name="count" :value="old('count')" min="1" required />
                            <x-input-error :messages="$errors->get('count')" class="mt-2" />
                        </div>

                        <!-- Suspected Cause -->
                        <div class="mt-4">
                            <x-input-label for="suspected_cause" :value="__('Suspected Cause')" />
                            <textarea id="suspected_cause" name="suspected_cause" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('suspected_cause') }}</textarea>
                            <x-input-error :messages="$errors->get('suspected_cause')" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('Additional Notes (Optional)')" />
                            <textarea id="notes" name="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Save Record') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('batch_id').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var stationId = selectedOption.getAttribute('data-station-id');
            var stationName = selectedOption.getAttribute('data-station-name');
            document.getElementById('station_id').value = stationId;
            document.getElementById('station_name').value = stationName;
        });
    </script>
</x-app-layout>