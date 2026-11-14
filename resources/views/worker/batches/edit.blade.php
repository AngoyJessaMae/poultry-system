<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Batch') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('worker.batches.update', $batch) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Batch Code -->
                        <div>
                            <x-input-label for="batch_code" :value="__('Batch Code')" />
                            <x-text-input id="batch_code" class="block mt-1 w-full" type="text" name="batch_code" :value="old('batch_code', $batch->batch_code)" required autofocus />
                            <x-input-error :messages="$errors->get('batch_code')" class="mt-2" />
                        </div>

                        <!-- Station -->
                        <div class="mt-4">
                            <x-input-label for="station_id" :value="__('Station')" />
                            <select id="station_id" name="station_id" class="block mt-1 w-full border-gray-300 focus:border-brand-orange focus:ring-brand-orange rounded-md shadow-sm">
                                @foreach(\App\Models\Station::all() as $station)
                                    <option value="{{ $station->id }}" @if($batch->station_id == $station->id) selected @endif>{{ $station->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('station_id')" class="mt-2" />
                        </div>

                        <!-- Arrival Date -->
                        <div class="mt-4">
                            <x-input-label for="arrival_date" :value="__('Arrival Date')" />
                            <x-text-input id="arrival_date" class="block mt-1 w-full" type="date" name="arrival_date" :value="old('arrival_date', $batch->arrival_date->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('arrival_date')" class="mt-2" />
                        </div>

                        <!-- Initial Quantity (immutable after registration) -->
                        <div class="mt-4">
                            <x-input-label for="initial_quantity" :value="__('Initial Quantity')" />
                            <p class="mt-1 text-sm text-gray-600">{{ $batch->initial_quantity }} (cannot be changed after registration)</p>
                            <p class="mt-1 text-sm text-gray-600">Current quantity is calculated from recorded mortality, sales, transfers, and manager corrections.</p>
                        </div>

                        <!-- Initial Weight (grams) -->
                        <div class="mt-4">
                            <x-input-label for="initial_weight_grams" :value="__('Initial Weight (grams)')" />
                            <x-text-input id="initial_weight_grams" class="block mt-1 w-full" type="number" step="0.01" name="initial_weight_grams" :value="old('initial_weight_grams', $batch->initial_weight_grams)" required min="0" />
                            <x-input-error :messages="$errors->get('initial_weight_grams')" class="mt-2" />
                        </div>

                        <p class="mt-4 rounded-md bg-orange-50 p-3 text-sm text-orange-800">Feeding schedule follows the selected station and cannot be changed here.</p>

                        <!-- Status -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-brand-orange focus:ring-brand-orange rounded-md shadow-sm">
                                <option value="active" {{ old('status', $batch->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="sold_out" {{ old('status', $batch->status) == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                                <option value="archived" {{ old('status', $batch->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('worker.batches.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-orange">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Update Batch') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
