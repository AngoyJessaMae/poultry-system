<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Mortality Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('worker.mortality-records.update', $mortalityRecord) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Batch -->
                        <div class="mt-4">
                            <x-input-label for="batch_id" :value="__('Batch')" />
                            <select id="batch_id" name="batch_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select a batch</option>
                                @foreach(\App\Models\Batch::where('current_quantity', '>', 0)->get() as $batch)
                                    <option value="{{ $batch->id }}" @if(old('batch_id', $mortalityRecord->batch_id) == $batch->id) selected @endif>{{ $batch->batch_code }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
                        </div>

                        <!-- Station -->
                        <div class="mt-4">
                            <x-input-label for="station_id" :value="__('Station')" />
                            <select id="station_id" name="station_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select a station</option>
                                @foreach(\App\Models\Station::all() as $station)
                                    <option value="{{ $station->id }}" @if(old('station_id', $mortalityRecord->station_id) == $station->id) selected @endif>{{ $station->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('station_id')" class="mt-2" />
                        </div>

                        <!-- Mortality Date -->
                        <div class="mt-4">
                            <x-input-label for="mortality_date" :value="__('Mortality Date')" />
                            <x-text-input id="mortality_date" class="block mt-1 w-full" type="date" name="mortality_date" :value="old('mortality_date', $mortalityRecord->mortality_date?->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('mortality_date')" class="mt-2" />
                        </div>

                        <!-- Count -->
                        <div class="mt-4">
                            <x-input-label for="count" :value="__('Number of Birds')" />
                            <x-text-input id="count" class="block mt-1 w-full" type="number" name="count" :value="old('count', $mortalityRecord->count)" min="1" required />
                            <x-input-error :messages="$errors->get('count')" class="mt-2" />
                        </div>

                        <!-- Suspected Cause -->
                        <div class="mt-4">
                            <x-input-label for="suspected_cause" :value="__('Suspected Cause')" />
                            <textarea id="suspected_cause" name="suspected_cause" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('suspected_cause', $mortalityRecord->suspected_cause) }}</textarea>
                            <x-input-error :messages="$errors->get('suspected_cause')" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('Additional Notes (Optional)')" />
                            <textarea id="notes" name="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $mortalityRecord->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('worker.mortality-records.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Update Record') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>