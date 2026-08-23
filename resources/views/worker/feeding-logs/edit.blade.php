<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Feeding Log') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('worker.feeding-logs.update', $feedingLog) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Batch -->
                        <div class="mt-4">
                            <x-input-label for="batch_id" :value="__('Batch')" />
                            <select id="batch_id" name="batch_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select a batch</option>
                                {{-- Include current batch even if its quantity is 0, since it was already assigned to this record --}}
                                @php
                                    $batches = \App\Models\Batch::where('current_quantity', '>', 0)
                                        ->orWhere('id', $feedingLog->batch_id)
                                        ->get();
                                @endphp
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}" @if(old('batch_id', $feedingLog->batch_id) == $batch->id) selected @endif>{{ $batch->batch_code }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
                        </div>

                        <!-- Station -->
                        <div class="mt-4">
                            <x-input-label for="station_id" :value="__('Station')" />
                            <select id="station_id" name="station_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select a station</option>
                                @foreach(\App\Models\Station::all() as $station)
                                    <option value="{{ $station->id }}" @if($feedingLog->station_id == $station->id) selected @endif>{{ $station->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('station_id')" class="mt-2" />
                        </div>

                        <!-- Feed Type -->
                        <div class="mt-4">
                            <x-input-label for="feed_type" :value="__('Feed Type')" />
                            <x-text-input id="feed_type" class="block mt-1 w-full" type="text" name="feed_type" :value="old('feed_type', $feedingLog->feed_type)" required />
                            <x-input-error :messages="$errors->get('feed_type')" class="mt-2" />
                        </div>

                        <!-- Quantity Fed (kg) -->
                        <div class="mt-4">
                            <x-input-label for="quantity_kg" :value="__('Quantity Fed (kg)')" />
                            <x-text-input id="quantity_kg" class="block mt-1 w-full" type="number" step="0.01" name="quantity_kg" :value="old('quantity_kg', $feedingLog->quantity_kg)" required />
                            <x-input-error :messages="$errors->get('quantity_kg')" class="mt-2" />
                        </div>

                        <!-- Fed At -->
                        <div class="mt-4">
                            <x-input-label for="fed_at" :value="__('Feeding Date & Time')" />
                            <x-text-input id="fed_at" class="block mt-1 w-full" type="datetime-local" name="fed_at" :value="old('fed_at', $feedingLog->fed_at->format('Y-m-d\TH:i'))" required />
                            <x-input-error :messages="$errors->get('fed_at')" class="mt-2" />
                        </div>

                        <!-- Feeding Time Slot (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="feeding_time_slot" :value="__('Time Slot (Optional)')" />
                            <x-text-input id="feeding_time_slot" class="block mt-1 w-full" type="text" name="feeding_time_slot" :value="old('feeding_time_slot', $feedingLog->feeding_time_slot)" />
                            <x-input-error :messages="$errors->get('feeding_time_slot')" class="mt-2" />
                        </div>

                        <!-- Notes (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" name="notes">{{ old('notes', $feedingLog->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('worker.feeding-logs.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Update Log') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>